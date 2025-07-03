<?php

namespace Plugin\Refund\Http\ApiResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Plugin\Refund\Http\ApiResource\RefundTrackingCollection;

class RefundRequestDetailsResource extends JsonResource
{

    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'refund_date' => $this->created_at->format('d M Y h:i:s A'),
                'total_amount' => $this->total_amount,
                'refunded_amount' => $this->total_refund_amount,
                'refund_code' => $this->refund_code,
                'current_return_status' => $this->return_status,
                'current_payment_status' => $this->refund_status,
                'return_status' => $this->returnStatusLabel(),
                'payment_status' => $this->paymentStatusLabel(),
                'order_details' => $this->getOrderDetails(),
                'product_details' => $this->getProductDetails(),
                'tracking_list' => new RefundTrackingCollection($this->trackings),
                'attachments' => $this->getAttachments(),
                'note' => $this->comment,
                'refund_reason' => $this->reason != null ? $this->reason->translation('name', session()->get('api_locale')) : null,
            ];
    }

    public function getOrderDetails()
    {
        $order = [];
        $order['id'] = $this->order->id;
        $order['order_date'] = $this->order->created_at->format('d M Y h:i:s A');
        $order['subtotal'] = $this->order->sub_total;
        $order['discount'] = $this->order->total_discount;
        $order['shipping_cost'] = $this->order->total_delivery_cost;
        $order['tax'] = $this->order->total_tax;
        $order['order_amount'] = $this->order->total_payable_amount;
        $order['order_code'] = $this->order->order_code;
        $order['paid_by'] = $this->order->payment_method_info->name;
        return $order;
    }

    public function getProductDetails()
    {
        $product = [];
        $product['name'] = $this->product->translation('name', session()->get('api_locale'));
        $product['permalink'] = $this->product->permalink;
        $product['image'] = getFilePath($this->product->thumbnail_image);
        $product['quantity'] = $this->quantity;
        $product['price'] = $this->total_amount;
        return $product;
    }

    public function getAttachments()
    {
        $images = substr($this->images, 1, -1);
        $images = explode(',', $images);
        $temp = [];
        foreach ($images as $image) {
            if ($image != null) {
                $path = getFilePath($image);

                array_push($temp, $path);
            }
        }
        return $temp;
    }

    public function with($request)
    {
        return
            [
                'success' => true,
                'status' => 200
            ];
    }
}
