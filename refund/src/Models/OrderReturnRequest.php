<?php

namespace Plugin\Refund\Models;

use Illuminate\Database\Eloquent\Model;
use Plugin\TlcommerceCore\Models\Orders;
use Plugin\TlcommerceCore\Models\Product;
use Plugin\TlcommerceCore\Models\Customers;
use Plugin\Refund\Models\ProductRefundReason;
use Plugin\TlcommerceCore\Models\OrderHasProducts;

class OrderReturnRequest extends Model
{

    protected $table = "tl_com_order_refund_requests";


    public function trackings()
    {
        return $this->hasMany(RefundRequestTracking::class, 'request_id')->orderBy('id', 'DESC');
    }

    public function customer()
    {
        return $this->hasOne(Customers::class,  'id', 'customer_id');
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, OrderHasProducts::class, 'id', 'id', 'ordered_product_id', 'product_id');
    }

    public function reason()
    {
        return $this->belongsTo(ProductRefundReason::class, 'reason_id', 'id');
    }

    public function order()
    {
        return $this->hasOne(Orders::class, 'id', 'order_id');
    }

    public function paymentStatusLabel()
    {
        if ($this->refund_status == config('tlecommercecore.return_request_payment_status.refunded')) {
            return 'refunded';
        } else {
            return 'pending';
        }
    }

    public function returnStatusLabel()
    {
        if ($this->return_status == config('tlecommercecore.return_request_status.approved')) {
            return 'approved';
        } else if ($this->return_status == config('tlecommercecore.return_request_status.approved')) {
            return 'pending';
        } else if ($this->return_status == config('tlecommercecore.return_request_status.processing')) {
            return 'processing';
        } else if ($this->return_status == config('tlecommercecore.return_request_status.cancelled')) {
            return 'cancelled';
        } else if ($this->return_status == config('tlecommercecore.return_request_status.product_received')) {
            return 'product received';
        } else {
            return 'processing';
        }
    }
}
