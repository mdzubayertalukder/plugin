<?php

namespace Plugin\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\Wallet\Models\BankInformation;

class OfflinePaymentMethod extends Model
{

    protected $table = "tl_com_wallet_payment_methods";

    public function bank_info()
    {
        return $this->hasOne(BankInformation::class, 'payment_method_id', 'id');
    }
}
