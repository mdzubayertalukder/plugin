<?php

namespace Plugin\TlcommerceCore\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturnRequest extends Model
{

    protected $table = "tl_com_order_refund_requests";


    public function paymentStatusLabel()
    {
        if ($this->refund_status == config('settings.return_request_payment_status.refunded')) {
            return 'refunded';
        } else {
            return 'pending';
        }
    }

    public function returnStatusLabel()
    {
        if ($this->return_status == config('settings.return_request_status.approved')) {
            return 'approved';
        } else if ($this->return_status == config('settings.return_request_status.approved')) {
            return 'pending';
        } else if ($this->return_status == config('settings.return_request_status.processing')) {
            return 'processing';
        } else if ($this->return_status == config('settings.return_request_status.cancelled')) {
            return 'cancelled';
        } else {
            return 'processing';
        }
    }
}
