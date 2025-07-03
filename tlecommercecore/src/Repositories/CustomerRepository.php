<?php

namespace Plugin\TlcommerceCore\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Plugin\TlcommerceCore\Models\Orders;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\TlcommerceCore\Models\CustomerAddress;
use Core\Http\Mail\CustomerResetEmail;
use Plugin\TlcommerceCore\Models\CustomerWishlist;
use Core\Http\Mail\CustomerForgotPassword;
use Core\Http\Mail\CustomerEmailVerification;
use Plugin\TlcommerceCore\Repositories\SettingsRepository;

class CustomerRepository
{

    /**
     * Will return customer list
     *
     * @param Object $request
     * @return Collections
     */
    public function customerList($request)
    {
        try {
            $data = [
                'tl_com_customers.id',
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.uid)) as uid'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.name)) as name'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.email)) as email'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.image)) as image'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.status)) as status'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.phone)) as phone'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_customers.phone_code)) as phone_code'),
                DB::raw('COUNT(DISTINCT(tl_com_orders.id)) as total_order'),
            ];
            $query = Customers::query()
                ->leftjoin('tl_com_orders', 'tl_com_orders.customer_id', '=', 'tl_com_customers.id')
                ->groupBy('tl_com_customers.id')
                ->orderBy('tl_com_customers.id', 'DESC')
                ->select($data);

            if ($request->has('join_date') && $request['join_date'] != null) {
                $date_range = explode(' to ', $request['join_date']);
                if (sizeof($date_range) > 1) {
                    $query = $query->whereBetween('tl_com_customers.created_at', $date_range);
                }
            }

            if ($request->has('status') && $request['status'] != null) {
                $query = $query->where('tl_com_customers.status', $request['status']);
            }

            if ($request->has('search') && $request['search'] != null) {
                $query = $query->where('tl_com_customers.name', 'like', '%' . $request['search'] . '%')
                    ->orWhere('tl_com_customers.email', 'like', '%' . $request['search'] . '%')
                    ->orWhere('tl_com_customers.uid', 'like', '%' . $request['search'] . '%')
                    ->orWhere('tl_com_customers.phone', 'like', '%' . $request['search'] . '%');
            }

            $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

            if ($per_page != null && $per_page == 'all') {
                $customers = $query->orderBy('tl_com_customers.id', 'DESC')
                    ->paginate($query->get()->count())
                    ->withQueryString();
            } else {
                $customers = $query->orderBy('tl_com_customers.id', 'DESC')
                    ->paginate($per_page)
                    ->withQueryString();
            }
            return $customers;
        } catch (\Exception $e) {
        }
    }

    /**
     * Will return customer dashboard details
     *
     * @param Int $customer_id
     * @return Array
     */
    public function customerDashboardDetails($customer_id)
    {
        $data = [
            'total_order' => Orders::where('customer_id', $customer_id)->count(),
            'total_successfull_order' => Orders::where('customer_id', $customer_id)->where('delivery_status', config('tlecommercecore.order_delivery_status.delivered'))->count(),
            'total_pending_order' => Orders::where('customer_id', $customer_id)->whereNotIn('delivery_status', [config('tlecommercecore.order_delivery_status.delivered')])->count(),
            'total_purchase_amount' => $this->customerTotalPurchase($customer_id),
            'last_purchase_date' => $this->customerLastPurchase($customer_id) != null ? $this->customerLastPurchase($customer_id)->created_at->format('d M Y') : null,
            'last_purchase_amount' => $this->customerLastPurchase($customer_id) != null ? $this->customerLastPurchase($customer_id)->total_payable_amount : 0,
            'current_month' => Carbon::now()->format('M Y'),
            'current_month_purchase' => $this->customerMonthwisePurchase($customer_id, Carbon::now()->startOfMonth()),
            'last_month' => Carbon::now()->startOfMonth()->subMonth(1)->format('M Y'),
            'last_month_purchase' => $this->customerMonthwisePurchase($customer_id, Carbon::now()->startOfMonth()->subMonth(1)),
            'total_wishlisted_product' => CustomerWishlist::where('customer_id', $customer_id)->count(),
            'total_support_tickets' => 0,
            'wallet_balance' => 0,

        ];

        return $data;
    }

    /**
     * Will return customer details
     *
     * @param Int $customer_id
     * @return Collection
     */
    public function customerDetails($customer_id)
    {
        return Customers::findOrFail($customer_id);
    }
    /**
     * Will return customer order list
     *
     * @param Int $customer_id
     * @return Collections
     */
    public function customerOrderList($customer_id)
    {
        try {
            $data = [
                'tl_com_orders.id',
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.total_payable_amount)) as total_payable_amount'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.order_code)) as order_code'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.created_at)) as created_at'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.read_at)) as read_at'),
                DB::raw('sum(tl_com_ordered_products.quantity) as total_product'),
                DB::raw('sum(tl_com_ordered_products.tax) as total_tax'),
                DB::raw('sum(tl_com_ordered_products.delivery_cost) as total_delivery_cost'),
            ];
            $query = DB::table('tl_com_orders')
                ->leftjoin('tl_com_ordered_products', 'tl_com_ordered_products.order_id', '=', 'tl_com_orders.id')
                ->groupBy('tl_com_orders.id')
                ->select($data);
            $products = $query->where('customer_id', $customer_id)->get();
            return $products;
        } catch (\Exception $e) {
            return [];
        }
    }
    /**
     * Will return customer return requests lists
     *
     * @param Int $customer_id
     * @return Collection
     */
    public function customerReturnRequests($customer_id)
    {
        try {
            $data = [
                'tl_com_order_refund_requests.id',
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.order_code)) as order_code'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_orders.id)) as order_id'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_order_refund_requests.refund_code)) as refund_code'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_order_refund_requests.total_refund_amount)) as total_refund_amount'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_order_refund_requests.created_at)) as created_at'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_order_refund_requests.refund_status)) as payment_status'),
                DB::raw('GROUP_CONCAT(DISTINCT(tl_com_order_refund_requests.return_status)) as return_status'),
            ];
            $query = DB::table('tl_com_order_refund_requests')
                ->leftjoin('tl_com_orders', 'tl_com_orders.id', '=', 'tl_com_order_refund_requests.order_id')
                ->groupBy('tl_com_order_refund_requests.id')
                ->select($data);
            $return_requests = $query->where('tl_com_order_refund_requests.customer_id', $customer_id)->get();
            return $return_requests;
        } catch (\Exception $e) {
            return [];
        }
    }
    /**
     * Customer total purchase
     *
     * @param Int $customer_id
     * @return mixed
     */
    public function customerTotalPurchase($customer_id)
    {
        return Orders::where('customer_id', $customer_id)->sum('total_payable_amount');
    }

    /**
     * Will return customer last purchase
     *
     * @param Int $customer_id
     * @return Collection
     */
    public function customerLastPurchase($customer_id)
    {
        return Orders::where('customer_id', $customer_id)->orderBy('id', 'DESC')->first();
    }

    /**
     * Customer month wise total purchase
     *
     * @param Int $customer_id
     *
     * @return mixed
     */
    public function customerMonthwisePurchase($customer_id, $start_date)
    {
        return Orders::where('customer_id', $customer_id)
            ->whereMonth("created_at", "=", $start_date)
            ->sum('total_payable_amount');
    }
    /**
     * Store customer information
     *
     * @param object $request
     * @return mixed
     */
    public function customerRegister($request)
    {
        try {
            DB::beginTransaction();
            $status = SettingsRepository::getEcommerceSetting('customer_auto_approved') == config('settings.general_status.active') && SettingsRepository::getEcommerceSetting('customer_email_varification') == config('settings.general_status.in_active') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $customer = new Customers;
            $customer->uid = date('hisymd');
            $customer->name = $request['name'];
            $customer->email = $request['email'];
            $customer->phone_code = $request['phone_code'];
            $customer->phone = $request['phone'];
            $customer->password = Hash::make($request['password']);
            $customer->status = $status;
            $customer->save();

            if (SettingsRepository::getEcommerceSetting('customer_email_varification') == config('settings.general_status.active')) {
                $identifier = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(32 / strlen($x)))), 1, 32);
                $id = Crypt::encryptString($customer->id);
                $verification_code = $identifier . '.' . $id;
                $customer->varification_code = $identifier;
                $customer->save();
                //send verification email
                $url = url('/') . '/customer/email-verification?u=' . $verification_code;

                $mail_data = [
                    'template_id' => 9,
                    'keywords' => getEmailTemplateVariables(9, true),
                    'subject' => getGeneralSetting('site_title') . ' Email Verification',
                    '_system_name_' =>  getGeneralSetting('site_title'),
                    '_customer_email_' => $request['email'],
                    '_email_verify_link_' => $url,
                ];

                Mail::to($request['email'])->send(new CustomerEmailVerification($mail_data));
            }
            DB::commit();
            return $customer;
        } catch (\Exception $e) {
            DB::rollBack();
            return NULL;
        } catch (\Error $e) {
            DB::rollBack();
            return NULL;
        }
    }
    /**
     * Will update customer basic information
     *
     * @param Int $customer_id
     * @param Object $request
     * @return Collection
     */
    public function updateCustomerBasicInfo($request, $customer_id)
    {
        try {
            $customer = Customers::find($customer_id);
            if ($customer != null) {
                $image = $customer->image;
                if ($request->has('edit_image')) {
                    $image = $request['edit_image'];
                } else {
                    if ($request->hasFile('image')) {
                        $image = saveFileInStorage($request['image'], false);
                    } else {
                        if ($request['old_image'] != 'null') {
                            $image = $customer->image;
                        } else {
                            $image = NULL;
                        }
                    }
                }

                if ($request->has('email')) {
                    $customer->email = $request['email'];
                }

                $customer->name = $request['name'];
                $customer->phone = $request['phone'];
                $customer->image = $image;
                $customer->save();
                return $customer;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send customer reset password link
     *
     * @param String $email
     * @return bool
     */
    public function customerResetPasswordLink($email)
    {
        try {
            $customer = Customers::where('email', $email)->first();
            $id_crypt = Crypt::encryptString($customer->id);
            $token = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10);
            $email_crypt = Crypt::encryptString($customer->email);
            $verification_code = $id_crypt . '.' . $email_crypt . '.' . $token;
            $url = url('/') . '/password/reset?u=' . $verification_code;

            $mail_data = [
                'template_id' => 7,
                'keywords' => getEmailTemplateVariables(7, true),
                'subject' => 'Password Reset Request',
                '_system_name_' =>  getGeneralSetting('site_title'),
                '_customer_email_' => $email,
                '_reset_url_' => $url,
            ];

            $customer->reset_password = $token;
            $customer->save();

            Mail::to($email)->send(new CustomerForgotPassword($mail_data));
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Send customer reset email link
     *
     * @param String $email
     * @return bool
     */
    public function customerResetEmailLink($email)
    {
        try {
            $customer = Customers::where('email', $email)->first();
            $id_crypt = Crypt::encryptString($customer->id);
            $token = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10);
            $email_crypt = Crypt::encryptString($customer->email);
            $verification_code = $id_crypt . '.' . $email_crypt . '.' . $token;
            $url = url('/') . '/email/reset?u=' . $verification_code;
            $mail_data = [
                'template_id' => 8,
                'keywords' => getEmailTemplateVariables(8, true),
                'subject' => 'Email Reset Request',
                '_system_name_' =>  getGeneralSetting('site_title'),
                '_reset_url_' => $url,
            ];

            $customer->reset_password = $token;
            $customer->save();

            Mail::to($email)->send(new CustomerResetEmail($mail_data));
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will verify customer reset password token
     *
     * @param Object $request
     * @return bool
     */
    public function verifyCustomerResetPasswordToken($request)
    {
        try {
            if ($request->has('identifier')) {
                $code_array = explode('.', $request['identifier']);
                $id = Crypt::decryptString($code_array[0]);
                $email = Crypt::decryptString($code_array[1]);
                $token = $code_array[2];
                $customer = Customers::where('id', $id)->where('email', $email)->where('reset_password', $token)->first();
                if ($customer != null) {
                    $customer->reset_password = NULL;
                    $customer->save();
                    return true;
                } else {
                    return false;
                }
            } else {
                return 'bb';
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * reset customer password
     *
     * @param Object $request
     * @return bool
     */
    public function customerResetPassword($request)
    {
        try {
            if ($request->has('identifier')) {
                $code_array = explode('.', $request['identifier']);
                $id = Crypt::decryptString($code_array[0]);
                $email = Crypt::decryptString($code_array[1]);
                $customer = Customers::where('id', $id)->where('email', $email)->first();
                if ($customer != null) {
                    $customer->password = Hash::make($request['password']);
                    $customer->save();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * reset customer password
     *
     * @param Int $customer_id
     * @param String $new_password
     * @return bool
     */
    public function changeCustomerPassword($customer_id, $new_password)
    {
        try {
            DB::beginTransaction();
            $customer = Customers::find($customer_id);
            if ($customer != null) {
                $customer->password = Hash::make($new_password);
                $customer->save();
                DB::commit();
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * reset customer email
     *
     * @param Object $request
     * @return bool
     */
    public function customerResetEmail($request)
    {
        try {
            if ($request->has('identifier')) {
                $code_array = explode('.', $request['identifier']);
                $id = Crypt::decryptString($code_array[0]);
                $email = Crypt::decryptString($code_array[1]);
                $customer = Customers::where('id', $id)->where('email', $email)->first();
                if ($customer != null) {
                    $customer->email = $request['email'];
                    $customer->save();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will verify customer email address
     *
     * @param String $verification_code
     * @return mixed
     */
    public function verifyCustomerEmail($verification_code)
    {
        try {
            $code_array = explode('.', $verification_code);
            $identifier = $code_array[0];
            $id = Crypt::decryptString($code_array[1]);
            $customer = Customers::where('id', $id)->where('varification_code', $identifier)->first();
            if ($customer != null) {
                $customer->status = config('settings.general_status.active');
                $customer->varification_code = NULL;
                $customer->verified_at = date("Y-m-d H:i:s");
                $customer->save();
                return $customer;
            } else {
                return NULL;
            }
        } catch (\Exception $e) {
            return NULL;
        } catch (\Error $e) {
            return NULL;
        }
    }
    /**
     * Will store customer address
     *
     * @param Object $request
     * @return bool
     */
    public function storeCustomerAddress($request)
    {
        try {
            $address = new CustomerAddress;
            $address->customer_id = auth('jwt-customer')->user()->id;
            $address->name = $request['name'];
            $address->phone = $request['phone'];
            $address->phone_code = $request['phone_code'];
            $address->country_id = $request['country'];
            $address->state_id = $request['state'];
            $address->city_id = $request['city'];
            $address->postal_code = $request['postal_code'];
            $address->address = $request['address'];
            $address->save();
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will update customer address
     *
     * @param Object $request
     * @return bool
     */
    public function updateCustomerAddress($request)
    {
        try {
            $address = CustomerAddress::where('id', $request['id'])->first();
            if ($address != null) {
                if ($request['default_shipping'] == config('settings.general_status.active')) {
                    CustomerAddress::where('customer_id', auth('jwt-customer')->user()->id)
                        ->where('id', '!=', $address->id)
                        ->update([
                            'default_shipping' => config('settings.general_status.in_active')
                        ]);
                }
                if ($request['default_billing'] == config('settings.general_status.active')) {
                    CustomerAddress::where('customer_id', auth('jwt-customer')->user()->id)
                        ->where('id', '!=', $address->id)
                        ->update([
                            'default_billing' => config('settings.general_status.in_active')
                        ]);
                }
                $address->customer_id = auth('jwt-customer')->user()->id;
                $address->name = $request['name'];
                $address->phone = $request['phone'];
                $address->phone_code = $request['phone_code'];
                $address->postal_code = $request['postal_code'];
                $address->address = $request['address'];
                $address->status = $request['status'];
                $address->default_shipping = $request['default_shipping'];
                $address->default_billing = $request['default_billing'];

                if (getEcommerceSetting('hide_country_state_city_in_checkout') != config('settings.general_status.active')) {
                    $address->country_id = $request['country'];
                    $address->state_id = $request['state'];
                    $address->city_id = $request['city'];
                }
                $address->save();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will delete customer address
     *
     * @param Int $address_id
     * @param Int $customer_id
     * @return bool
     */
    public function deleteCustomerAddress($address_id, $customer_id)
    {
        try {
            DB::beginTransaction();
            $address = CustomerAddress::where('id', $address_id)->where('customer_id', $customer_id)->first();
            if ($address != null) {
                DB::commit();
                $address->delete();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return customer all address
     *
     * @param Int $customer_id
     * @return Collections
     */
    public function customerAllAddress($customer_id)
    {
        try {
            return CustomerAddress::with(['country', 'state', 'city'])
                ->where('customer_id', $customer_id)
                ->get();
        } catch (\Exception $e) {
            return [];
        } catch (\Error $e) {
            return [];
        }
    }

    /**
     * Will return customer wallet transactions
     *
     * @param Int $customer_id
     * @return Collections
     */
    public function customerWalletTransaction($customer_id)
    {
        try {
            if (isActivePluging('wallet')) {
                $transactions = \Plugin\Wallet\Models\WalletTransaction::where('customer_id', $customer_id)->orderBy('id', 'DESC')->get();

                return $transactions;
            } else {

                return [];
            }
        } catch (\Exception $e) {
            return [];
        } catch (\Error $e) {
            return [];
        }
    }
    /**
     * Will return customer address details
     *
     * @param Int $address_id
     * @return Collection
     */
    public function customerAddressDetails($address_id)
    {
        return CustomerAddress::findOrFail($address_id);
    }
    /**
     * Will update customer status
     *
     * @param Int $customer_id
     *
     * @return bool
     */
    public function changeStatus($customer_id)
    {
        try {
            $customer = Customers::find($customer_id);
            if ($customer != null) {
                $updated_status = $customer->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
                $customer->status = $updated_status;
                $customer->save();
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will delete a customer
     *
     * @param Int $customer_id
     * @return bool
     */
    public function deleteCustomer($customer_id)
    {
        try {
            DB::beginTransaction();
            $customer = Customers::find($customer_id);
            if ($customer != null) {
                $customer->delete();
            } else {
                DB::rollBack();
                return false;
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
}
