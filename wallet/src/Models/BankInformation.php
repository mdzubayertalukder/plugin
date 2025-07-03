<?php

namespace Plugin\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class BankInformation extends Model
{

    protected $table = "tl_com_wallet_bank_information";

    protected $fillable = ['payment_method_id'];
}
