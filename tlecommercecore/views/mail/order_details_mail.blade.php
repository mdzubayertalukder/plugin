@php
    $order_details = \Plugin\TlcommerceCore\Models\Orders::findOrFail($order_id);
@endphp
<table border="0" align="center" cellpadding="0" cellspacing="0"
    style="max-width:670px;background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);width:100%;margin-top:10px;padding:10px 15px">
    <tr>
        @if ($order_details->shipping_details != null)
            <td>
                <p style="font-weight:700">Delivery Details</p>
                <p style="font-size:16px">Name: {{ $order_details->shipping_details->name }}</p>
                <p style="font-size:16px">Address: {{ $order_details->shipping_details->address }},
                    @if ($order_details->shipping_details->city != null)
                        {{ $order_details->shipping_details->city->name }},
                    @endif
                    @if ($order_details->shipping_details->state != null)
                        {{ $order_details->shipping_details->state->name }},
                    @endif
                    @if ($order_details->shipping_details->country != null)
                        {{ $order_details->shipping_details->country->name }}
                    @endif
                </p>
                <p style="font-size:16px">Phone:
                    {{ $order_details->shipping_details->phone_code }}{{ $order_details->shipping_details->phone }}</p>
            </td>
        @endif
        @if (isActivePluging('pickuppoint'))
            @if ($order_details->pickup_point != null)
                <td>
                    <p style="font-weight:700">Pickup Point Details</p>
                    <p style="font-size:16px">Pickup Point: {{ $order_details->pickup_point->name }}</p>
                    <p style="font-size:16px">Address:
                        {{ $order_details->pickup_point->location }}
                    </p>
                    <p style="font-size:16px">Phone:
                        {{ $order_details->pickup_point->phone }}
                    </p>
                </td>
            @endif
        @endif
    </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="0"
    style="max-width:670px;background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);width:100%;margin-top:10px;padding:10px 15px">
    <tr>
        <td>
            <p style="font-weight:700">Order Details</p>
            @foreach ($order_details->products as $key => $product)
                <div class="order-item" style="margin-bottom:10px">
                    <p>ITEM {{ $key + 1 }}</p>
                    <div style="display: flex;">
                        <div>
                            <div class="title">
                                <h5
                                    style="margin: 0px;font-size:17px;margin-bottom: 5px;font-weight:500;text-transform: capitalize">
                                    {{ $product->product_details->name }}</h5>
                                @if ($product->variant_id != null)
                                    <div class="variant" style="margin-bottom: 3px">
                                        <span style="text-transform: capitalize">
                                            {{ $product->variant_id }}
                                        </span>
                                    </div>
                                @endif
                                @if ($product->attachment != null)
                                    <div class="attatchment" style="margin-bottom: 3px">
                                        <a href="{{ getFilePath($product->attachment) }}"
                                            target="_blank">{{ translate('Download attatchment') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="price"><span>{!! currencyExchange($product->unit_price) !!}</span></div>
                            <div class="quantity">
                                <span>x {{ $product->quantity }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </td>
    </tr>
</table>
<table border="0" align="center" cellpadding="0" cellspacing="0"
    style="max-width:670px;background:#fff; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);width:100%;margin-top:10px;padding:10px 15px;margin-bottom:10px">
    <tr>
        <td>
            <p style="font-size:16px">Order Total:</p>
            <p style="font-size:16px">Delivery Fee:</p>
            <p style="font-size:16px">Total Discount:</p>
            <p style="font-size:16px;font-weight:700;margin-bottom:20px">Total Payment (VAT Incl):</p>
            <p style="font-size:16px">Paid By:</p>
        </td>
        <td style="text-align:right">
            <p style="font-size:16px">{!! currencyExchange($order_details->sub_total) !!}</p>
            <p style="font-size:16px">{!! currencyExchange($order_details->total_delivery_cost) !!}</p>
            <p style="font-size:16px">{!! currencyExchange($order_details->total_discount) !!}</p>
            <p style="font-size:16px;font-weight:700;margin-bottom:20px">
                {!! currencyExchange($order_details->total_payable_amount) !!}</p>
            <p style="font-size:16px">
                @if ($order_details->wallet_payment == config('settings.general_status.active'))
                    <span class="black">
                        {{ translate('Wallet') }}
                    </span>
                @else
                    <span class="black">
                        {{ $order_details->payment_method_info->name }}
                    </span>
                @endif
            </p>
        </td>
    </tr>
</table>
