<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shipping Lebel Preview</title>
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
            margin: 10px;
            padding: 10px;
            font-size: 16px;
            color: #000;
        }

        .big-barcode {
            height: 150px;
            width: 100%;
        }

        .small-barcode {
            height: 100px;
        }

        .qr-code {
            min-height: 300px;
            max-height: 350px;
        }



        .currency {
            font-family: '{{ $currency_font }}', sans-serif;
        }

        .table tr,
        td {
            padding: 20px 10px !important;
        }

        .table-border-less {
            border: 0px;
        }

        .border-bottom-1 {
            border-bottom: 1px solid #000 !important;
            padding: 5px;
        }

        .border-right-1 {
            border-right: 1px solid #dee2e6 !important;
            padding: 5px;
        }

        .w-100 {
            width: 100%;
        }

        .p-0 {
            padding: 0px !important;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered w-100">
                <tbody>
                    <tr>
                        <td style="text-align: left;">{{ $order_info['date'] }}</td>
                        <td>
                            <p class="mb-0">{{ translate('Order Number', getLocale()) }}</p>
                            <p class="mb-0">{{ $order_info['order_code'] }}</p>
                        </td>
                        <td>
                            @if ($order_info['system_properties']['logo'] != null)
                                <img src="{{ $order_info['system_properties']['logo'] }}"
                                    alt="{{ $order_info['system_properties']['title'] }}">
                            @else
                                <h2>{{ $order_info['system_properties']['title'] }}</h2>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:center;">
                            <img src="data:image/png;base64, {!! $tracking_id_bar_code !!}" class="w-100 small-barcode">
                            <p class="mb-0 text-center" style="text-align: center;">
                                {{ translate('Tracking Number', getLocale()) }} {{ $order_info['tracking_id'] }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <img src="data:image/png;base64, {!! $order_code_bar_code !!}" class="w-100 big-barcode">
                            <p class="mb-0 text-center">
                                {{ translate('Order Number', getLocale()) }}
                                {{ $order_info['order_code'] }}
                            </p>
                        </td>
                        <td class="p-0" style="padding: 0px;">
                            <table class="w-100 p-0 table-border-less">
                                <tr>
                                    <td class="text-right">
                                        {{ $order_info['shipping_zone'] != null ? $order_info['shipping_zone'] : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        {{ $order_info['shipping_type'] != null ? $order_info['shipping_type'] : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        {{ $order_info['shipping_method'] != null ? $order_info['shipping_method'] : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">{{ $order_info['payment_method'] }}</td>
                                </tr>
                                <tr>
                                    <td class="currency text-right">
                                        {{ currencyExchange($order_info['total_payable_amount'], true, null, false) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="{{ $qr_code }}" class="qr-code float-left w-100">
                        </td>
                        <td colspan="2" class="p-0" style="padding: 0px;">
                            <table class="w-100 p-0 table-border-less">
                                <tr>
                                    <td class="text-right" colspan="2">
                                        @if ($order_info['shipping_info'] != null)
                                            <p class="mb-0">
                                                {{ $order_info['shipping_info']['name'] }}
                                                :{{ translate('Recipient', getLocale()) }}
                                            </p>
                                            <p>
                                                {{ $order_info['shipping_info']['phone'] }}
                                                :{{ translate('Tel', getLocale()) }} <br>
                                                {{ $order_info['shipping_info']['postal_code'] }}:
                                                {{ translate('Postal Code', getLocale()) }}
                                                {{ $order_info['shipping_info']['state'] }},
                                                {{ $order_info['shipping_info']['city'] }},
                                                {{ $order_info['shipping_info']['address'] }}
                                                {{ $order_info['shipping_info']['country'] }}<br>
                                            </p>
                                        @else
                                            {{ translate('Recipient', getLocale()) }}:
                                            {{ $order_info['customer_name'] }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class=" text-right" colspan="2">
                                        <p class="mb-0">
                                            {{ $order_info['system_properties']['title'] }}:
                                            {{ translate('Seller', getLocale()) }}
                                        </p>
                                        <p>
                                            {{ $order_info['system_properties']['phone'] }}
                                            :{{ translate('Tel', getLocale()) }}<br>
                                            {{ $order_info['system_properties']['address'] }},
                                            {{ $order_info['system_properties']['email'] }},
                                        </p>

                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="2">
                                        {{ translate('kg', getLocale()) }}
                                        {{ $order_info['total_product_weight'] }}
                                        {{ translate('Total Weight', getLocale()) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="2">
                                        {{ $order_info['num_of_products'] }}
                                        {{ translate('Number of Products', getLocale()) }}
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
