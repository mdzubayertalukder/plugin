<?php

namespace Plugin\Wallet\Models;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\Wallet\Models\OfflinePaymentMethod;
use Plugin\TlcommerceCore\Models\PaymentMethods;

class WalletTransaction extends Model
{

    protected $table = "tl_com_wallet_recharges";

    protected $appends = ['payment_method'];

    public function getStatusLabelAttribute()
    {
        if ($this->status == config('tlecommercecore.wallet_transaction_status.accept')) {
            return 'accepted';
        } else if ($this->status == config('tlecommercecore.wallet_transaction_status.declined')) {
            return 'declined';
        } else {
            return 'pending';
        }
    }

    public function getPaymentMethodAttribute()
    {
        if ($this->recharge_type == config('tlecommercecore.wallet_recharge_type.online')) {
            $method = PaymentMethods::where('id', $this->payment_method_id)->first();
            if ($method != null) {
                return $method->name;
            } else {
                return null;
            }
        } else if ($this->recharge_type == config('tlecommercecore.wallet_recharge_type.offline')) {
            $method = OfflinePaymentMethod::where('id', $this->payment_method_id)->first();
            if ($method != null) {
                return $method->name;
            } else {
                return null;
            }
        } else if ($this->recharge_type == config('tlecommercecore.wallet_recharge_type.manual')) {
            return 'manual';
        } else if ($this->recharge_type == config('tlecommercecore.wallet_recharge_type.cart')) {
            return 'cart';
        } else if ($this->recharge_type == config('tlecommercecore.wallet_recharge_type.cashback')) {
            return 'cashback';
        } else if ($this->recharge_type == config('tlecommercecore.wallet_recharge_type.refund')) {
            return 'refund';
        } else {
            return null;
        }
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function modifier()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
