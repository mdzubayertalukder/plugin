<?php

namespace Plugin\Wallet\Repositories;

use Illuminate\Support\Facades\DB;
use Plugin\Wallet\Models\BankInformation;
use Plugin\Wallet\Models\WalletTransaction;
use Plugin\Wallet\Models\OfflinePaymentMethod;
use Plugin\TlcommerceCore\Models\PaymentMethods;
use Plugin\Wallet\Repositories\WalletNotification;
use Illuminate\Contracts\Database\Eloquent\Builder;

class WalletRepository
{

    /**
     * Will return offline payment methods
     * 
     * @return Collections
     */
    public function offlineMethods()
    {
        return OfflinePaymentMethod::orderBy('id', 'DESC')->paginate(10)->withQueryString();
    }
    /**
     * Will return offline payment methods
     * 
     * @return Collection
     */
    public function activeOfflinePaymentMethods()
    {
        return OfflinePaymentMethod::where('status', config('settings.general_status.active'))->get();
    }

    /**
     * Will return active online payment methods
     * 
     * @return Collections  
     */
    public function activeOnlinePaymentMethods()
    {
        return PaymentMethods::whereNotIn('id', [config('tlecommercecore.payment_methods.cod')])->get()->map(function ($item) {
            return [
                'name' => $item->name,
                'logo' => getFilePath($item->logo, false),
                'id' => $item->id
            ];
        });
    }

    /**
     * Will store offline payment method
     * 
     * @param Object $request
     * @return bool
     */
    public function storeOfflinePayment($request)
    {
        try {
            $payment_method = new OfflinePaymentMethod;
            $payment_method->type = $request['method_type'];
            $payment_method->name = $request['name'];
            $payment_method->logo = $request['payment_image'];
            $payment_method->instruction = $request['instruction'];
            $payment_method->save();

            if ($request['method_type'] == config('tlecommercecore.offline_payment_type.bank')) {
                $bank_info = new BankInformation;

                $bank_info->payment_method_id = $payment_method->id;
                $bank_info->bank_name = $request['bank_name'];
                $bank_info->account_name = $request['account_name'];
                $bank_info->account_number = $request['account_number'];
                $bank_info->routing_number = $request['routing_number'];
                $bank_info->save();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will update offline payment method
     * 
     * @param Object $request
     * @return bool
     */
    public function updateOfflinePayment($request)
    {
        try {
            DB::beginTransaction();
            $payment_method = OfflinePaymentMethod::find($request['id']);
            $payment_method->type = $request['method_type'];
            $payment_method->name = $request['name'];
            $payment_method->logo = $request['edit_payment_image'];
            $payment_method->instruction = $request['instruction'];
            $payment_method->status = $request['status'];
            $payment_method->save();

            if ($request['method_type'] == config('tlecommercecore.offline_payment_type.bank')) {
                $bank_info = BankInformation::firstOrCreate(['payment_method_id' => $request['id']]);
                $bank_info->bank_name = $request['bank_name'];
                $bank_info->account_name = $request['account_name'];
                $bank_info->account_number = $request['account_number'];
                $bank_info->routing_number = $request['routing_number'];
                $bank_info->save();
            }
            if ($request['method_type'] != config('tlecommercecore.offline_payment_type.bank') && $payment_method->bank_info != null) {
                $payment_method->bank_info->delete();
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

    /**
     * Will delete offline payment method
     * 
     * @param Int $id
     * @return bool
     */
    public function deletePaymentMethod($id)
    {
        try {
            DB::beginTransaction();
            $payment_method = OfflinePaymentMethod::find($id);
            if ($payment_method != null) {
                if ($payment_method->bank_info != null) {
                    $payment_method->bank_info->delete();
                }
                $payment_method->delete();
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

    /**
     * Will store wallet payment transaction
     * 
     * @param Array $data
     * @return bool
     */
    public function storeWalletTraction($data)
    {
        try {
            DB::beginTransaction();
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->entry_type = $data['entry_type'];
            $wallet_transaction->recharge_type = $data['recharge_type'];
            $wallet_transaction->customer_id = $data['customer_id'];
            $wallet_transaction->added_by = $data['added_by'];
            $wallet_transaction->document = $data['document'];
            $wallet_transaction->recharge_amount = $data['recharge_amount'];
            $wallet_transaction->status = $data['status'];
            $wallet_transaction->payment_method_id = $data['payment_method_id'];
            $wallet_transaction->transaction_id = $data['transaction_id'];
            $wallet_transaction->save();

            //Send notification to customer
            $message = "";
            if ($data['entry_type'] == config('tlecommercecore.wallet_entry_type.credit')) {
                $message = currencyExchange($data['recharge_amount']) . " credited to your wallet";
            }
            if ($data['entry_type'] == config('tlecommercecore.wallet_entry_type.debit')) {
                $message = currencyExchange($data['recharge_amount']) . " debited from your wallet";
            }
            WalletNotification::sendWalletStatusUpdateNotification($data['customer_id'], $message);

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

    /**
     * Will return customer wallet transaction
     * 
     * @param Int $customer_id
     * @return Collections
     */
    public function customerWalletTransactions($customer_id, $request)
    {
        if ($request->has('perPage')) {
            return WalletTransaction::where('customer_id', $customer_id)->orderBy('id', 'DESC')->paginate($request['perPage']);
        } else {
            return WalletTransaction::where('customer_id', $customer_id)->orderBy('id', 'DESC')->get();
        }
    }
    /**
     * Will return customer wallet summary
     * 
     * @param Int $customer_id
     * @return Array
     */
    public function customerWalletSummary($customer_id)
    {
        try {
            $customer_wallet_transactions = WalletTransaction::where('customer_id', $customer_id)->get();
            $total_credit = collect($customer_wallet_transactions)->count() > 0 ? $customer_wallet_transactions->where('entry_type', config('tlecommercecore.wallet_entry_type.credit'))
                ->where('status', config('tlecommercecore.wallet_transaction_status.accept'))
                ->sum('recharge_amount') : 0;
            $total_debit = collect($customer_wallet_transactions)->count() > 0 ? $customer_wallet_transactions->where('entry_type', config('tlecommercecore.wallet_entry_type.debit'))
                ->where('status', config('tlecommercecore.wallet_transaction_status.accept'))
                ->sum('recharge_amount') : 0;
            $total_pending = collect($customer_wallet_transactions)->count() > 0 ? $customer_wallet_transactions->where('entry_type', config('tlecommercecore.wallet_entry_type.credit'))
                ->where('status', config('tlecommercecore.wallet_transaction_status.pending'))
                ->sum('recharge_amount') : 0;
            $data = [];
            $data['total_pending'] = $total_pending;
            $data['total_available'] = $total_credit - $total_debit;
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will return wallet transactions
     * 
     * @param Object $request
     * @return Collections
     */

    public function walletTransaction($request)
    {
        $query = WalletTransaction::with('customer')->with('modifier');

        if ($request->has('search') && $request['search'] != null) {
            $query = $query->whereHas('customer', function (Builder $query) use ($request) {
                $query->where('name', 'like', '%' . $request['search'] . '%');
            })->orWhere('transaction_id', 'like', '%' . $request['search'] . '%');
        }

        if ($request->has('transaction_type') && $request['transaction_type'] != null) {
            $query = $query->where('entry_type', $request['transaction_type']);
        }

        if ($request->has('payment_option') && $request['payment_option'] != null) {
            $query = $query->where('recharge_type', $request['payment_option']);
        }
        if ($request->has('status') && $request['status'] != null) {
            $query = $query->where('status', $request['status']);
        }

        return $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();
    }
    /**
     * Status change bulk actions
     * 
     * @param Object $request
     * @return bool
     */
    public function bulkStatusUpdate($request)
    {
        try {
            DB::beginTransaction();
            $items = $request['data']['selected_items'];
            foreach ($items as $item) {
                $transaction = WalletTransaction::find($item);
                $transaction->status = $request['data']['action'];
                $transaction->save();
                //Send Notification to customer
                $message = "Wallet recharge status updated";
                if ($request['data']['action'] == config('tlecommercecore.wallet_transaction_status.declined')) {
                    $message = "Your wallet transaction has been declined";
                }
                if ($request['data']['action'] == config('tlecommercecore.wallet_transaction_status.accept')) {
                    $message = "Your wallet transaction has been accepted";
                }
                if ($request['data']['action'] == config('tlecommercecore.wallet_transaction_status.pending')) {
                    $message = "Your wallet transaction has been pending";
                }
                WalletNotification::sendWalletStatusUpdateNotification($transaction->customer_id, $message);
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
