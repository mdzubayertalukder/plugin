<?php

namespace Plugin\Multivendor\Models;


use Core\Models\User;
use Illuminate\Database\Eloquent\Model;

class SellerPayoutRequests extends Model
{
    protected $table = "tl_com_seller_payout_request";

    protected $casts = ['payment_date' => 'datetime'];

    public function seller()
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }
}
