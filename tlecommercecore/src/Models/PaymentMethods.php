<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{

    protected $table = "tl_com_payment_methods";

    public function config()
    {
        return $this->hasMany(PaymentMethodConfig::class, 'payment_method_id');
    }
}
