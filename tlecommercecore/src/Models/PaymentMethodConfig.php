<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodConfig extends Model
{
    protected $table = "tl_com_payment_method_configs";

    protected $fillable = ['key_name', 'payment_method_id', 'key_value'];
}
