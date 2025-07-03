<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ======= MAIN STYLES ======= -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- ======= END MAIN STYLES ======= -->
    <style>
        @font-face {
            font-family: "Arabic";
            src: url("https://tlcommerce.themelooks.us/public/cdn/font/arabic.ttf") format('truetype');
        }

        @font-face {
            font-family: "Bangla";
            src: url("https://tlcommerce.themelooks.us/public/cdn/font/Nikosh.ttf") format('truetype');
        }

        @font-face {
            font-family: "Hibrew";
            src: url("https://tlcommerce.themelooks.us/public/cdn/font/Hebrew.ttf") format('truetype');
        }

        @font-face {
            font-family: "Arial Unicode MS";
            src: url("https://tlcommerce.themelooks.us/public/cdn/font/arial-unicode-ms.ttf") format('truetype');
        }

        body {
            font-family: '{{ $font_family }}', sans-serif;
            line-height: 20px;
        }

        .big-barcode {
            height: 150px;
            width: 100%;
        }

        .small-barcode {
            height: 100px;
        }

        .qr-code {
            min-height: 200px;
        }

        .border-bottom-1 {
            border-bottom: 1px solid #dee2e6 !important;
            padding: 5px;
        }

        .border-right-1 {
            border-right: 1px solid #dee2e6 !important;
        }

        .currency {
            font-family: '{{ $currency_font }}', sans-serif;
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered w-100">
                <tbody>
                    <tr>
                        <td>
                            @if ($order_info['system_properties']['logo'] != null)
                                <img src="{{ $order_info['system_properties']['logo'] }}"
                                    alt="{{ $order_info['system_properties']['title'] }}">
                            @else
                                <h2>{{ $order_info['system_properties']['title'] }}</h2>
                            @endif
                        </td>
                        <td>
                            <p class="mb-0">{{ translate('Order Number', getLocale()) }}</p>
                            <p class="mb-0">{{ $order_info['order_code'] }}</p>
                        </td>
                        <td style="text-align: right;">{{ $order_info['date'] }}</td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <img src="data:image/png;base64, {!! $tracking_id_bar_code !!}" class="w-100 small-barcode">
                            <p class="mb-0 text-center">
                                {{ translate('Tracking Number', getLocale()) }} {{ $order_info['tracking_id'] }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <img src="data:image/png;base64, {!! $order_code_bar_code !!}" class="w-100 big-barcode">
                            <p class="mb-0 text-center">
                                {{ translate('Order Number', getLocale()) }}
                                {{ $order_info['order_code'] }}
                            </p>
                        </td>
                        <td class="p-0" style="padding: 0px;">
                            <table class="w-100">
                                <tr>
                                    <td class="border-bottom-1">
                                        {{ $order_info['shipping_zone'] != null ? $order_info['shipping_zone'] : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-bottom-1">
                                        {{ $order_info['shipping_type'] != null ? $order_info['shipping_type'] : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-bottom-1">
                                        {{ $order_info['shipping_method'] != null ? $order_info['shipping_method'] : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-bottom-1">{{ $order_info['payment_method'] }}</td>
                                </tr>
                                <tr>
                                    <td class="currency" style="padding: 5px;">
                                        {{ currencyExchange($order_info['total_payable_amount'], true, null, false) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="data:image/png;base64, {!! $qr_code !!}" class="w-100 qr-code">
                        </td>

                        <td class="p-0" colspan="2" style="padding: 0px;">
                            <table class="w-100">
                                <tr>
                                    <td class="border-bottom-1" colspan="2">
                                        @if ($order_info['shipping_info'] != null)
                                            <p class="mb-0">
                                                {{ translate('Recipient', getLocale()) }}:
                                                {{ $order_info['shipping_info']['name'] }}
                                                ({{ translate('Tel', getLocale()) }}:{{ $order_info['shipping_info']['phone'] }})<br>
                                                {{ $order_info['shipping_info']['address'] }},
                                                {{ $order_info['shipping_info']['city'] }},
                                                {{ $order_info['shipping_info']['state'] }},
                                                {{ $order_info['shipping_info']['country'] }}<br>
                                                {{ translate('Postal Code', getLocale()) }}:
                                                {{ $order_info['shipping_info']['postal_code'] }}
                                            </p>
                                        @else
                                            {{ translate('Recipient', getLocale()) }}:
                                            {{ $order_info['customer_name'] }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class=" border-bottom-1" colspan="2">
                                        <p class="mb-0">
                                            {{ translate('Seller', getLocale()) }}:{{ $order_info['system_properties']['title'] }}
                                            ({{ translate('Tel', getLocale()) }}:{{ $order_info['system_properties']['phone'] }})<br>
                                            {{ $order_info['system_properties']['address'] }},
                                            {{ $order_info['system_properties']['email'] }},
                                        </p>

                                    </td>
                                </tr>
                                <tr>
                                    <td class=" border-bottom-1" colspan="2">
                                        {{ translate('Total Weight', getLocale()) }}
                                        {{ $order_info['total_product_weight'] }}
                                        {{ translate('kg', getLocale()) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px;" colspan="2">
                                        {{ translate('Number of Products', getLocale()) }}
                                        {{ $order_info['num_of_products'] }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
