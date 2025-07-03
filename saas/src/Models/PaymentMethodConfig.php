<?php

namespace Plugin\Saas\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodConfig extends Model
{

    protected $table = "tl_saas_payment_method_has_settings";

    protected $fillable = ['key_name', 'payment_method_id'];
}
