<?php

namespace Plugin\Saas\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugin\Saas\Models\PaymentMethods;
use Plugin\Saas\Models\PaymentMethodConfig;
use Plugin\Saas\Models\TenantPaymentMethod;

class PaymentMethodRepository
{
    /**
     * Will return all payment methods
     * 
     * @return Collections
     */
    public function paymentMethods($status = NULL)
    {
        try {
            if ($status != NULL) {
                return PaymentMethods::where('status', $status)->get();
            } else {
                return PaymentMethods::all();
            }
        } catch (\Exception $e) {
            return [];
        }
    }
    /**
     * Will activate or deactivate payment method
     * 
     * @param Int $id
     * @return bool
     */
    public function paymentMethodUpdateStatus($id)
    {
        try {
            DB::beginTransaction();
            $method = PaymentMethods::findOrFail($id);
            $updated_status = $method->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $method->status = $updated_status;
            $method->save();
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

    //Update Tenant Payment Method Status
    public function tenantPaymentMethodUpdateStatus($id)
    {
        try {
            DB::beginTransaction();
            $method = TenantPaymentMethod::findOrFail($id);
            $updated_status = $method->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $method->status = $updated_status;
            $method->save();
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
     * Will return payment method settings
     * 
     * @param Int $payment_method_id
     * @param String $key
     * @return String
     */
    public static function configKeyValue($payment_method_id, $key)
    {
        try {
            $config = PaymentMethodConfig::firstOrCreate(['payment_method_id' => $payment_method_id, 'key_name' => $key]);
            $config->save();
            return $config->key_value;
        } catch (\Exception $e) {
            return NULL;
        } catch (\Error $e) {
            return NULL;
        }
    }
    /**
     * Will update payment method credential
     * 
     * @param Object $request
     * @return bool
     */
    public function updatePaymentMethodCredential(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->has('payment_id')) {
                $data = $request->toArray();
                $array_keys = array_keys($data);
                foreach ($array_keys as $input_field) {
                    if ($input_field != 'payment_id' && $input_field != 'sandbox' && $input_field != 'paddle_recurring_payment') {
                        $config = PaymentMethodConfig::firstOrCreate(['payment_method_id' => $request['payment_id'], 'key_name' => $input_field]);
                        $config->key_value = $request[$input_field];
                        $config->save();
                    }
                }
                //update sandbox
                if (PaymentMethodConfig::where('key_name', 'sandbox')->where('payment_method_id', $request['payment_id'])->exists()) {
                    $sand_box = $request->has('sandbox') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                    $config = PaymentMethodConfig::where('payment_method_id', $request['payment_id'])->where('key_name', 'sandbox')->first();
                    $config->key_value = $sand_box;
                    $config->save();
                }
                //Update paddle recurring payment status
                if (isActivePluging('paddle-recurring') && $request->has('paddle_recurring_payment')) {
                    $paddle_recurring_payment = $request->has('paddle_recurring_payment') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                    $recurring_config = PaymentMethodConfig::where('payment_method_id', $request['payment_id'])->where('key_name', 'paddle_recurring_payment')->first();
                    $recurring_config->key_value = $paddle_recurring_payment;
                    $recurring_config->save();
                }
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
}
