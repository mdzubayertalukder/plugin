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
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/fonts/roboto/roboto.css') }}">
    <!-- ======= END MAIN STYLES ======= -->
    <style>
        html {
            margin: 0px;
            background: white;
        }

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
        }

        .qr-code {
            max-height: 120px;
        }

        .invoice-top {
            background: #EBEBEB;
        }

        .invoice-p {
            margin-bottom: 0px;
            font-size: 12px;
            color: black;
            padding-block: 5px !important;
        }

        .currency {
            font-family: '{{ $currency_font }}', sans-serif;
        }

        .payment-image {
            max-height: 150px;
        }

        .invoice-product-table {
            width: calc(100% - 40px);
            margin-inline: 20px;
        }

        .p5 {
            padding: 5px;
        }

        .invoice-product-table .thead-light th {
            color: black;
        }

        .invoice-title {
            font-size: 32px
        }
    </style>
</head>

<body>
    <table class="table table-borderless invoice-top">
        <tbody>
            <tr>
                <td>
                    @if ($admin_logo != null)
                        <img src="{{ getFilePath($admin_logo) }}" alt={{ $site_title }}>
                    @else
                        <h2>{{ $site_title }}</h2>
                    @endif
                </td>
                <td class="text-right">
                    <p class="invoice-title">{{ translate('INVOICE', getLocale()) }}</p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="text-right">
                    <p class="invoice-p">{{ translate('Payment ID', getLocale()) }}:
                        {{ $payment_history->pid }}</p>
                    <p class="invoice-p">{{ translate('Payment date', getLocale()) }}:
                        {{ $payment_history->updated_at }}
                    </p>
                    <p class="invoice-p">{{ translate('Payment method', getLocale()) }}:
                        {{ $payment_history->method }}</p>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table invoice-product-table p5">
        <thead class="thead-light">
            <tr>
                <td scope="col" class="invoice-p">{{ translate('Title', getLocale()) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td scope="col" class="text-right invoice-p">{{ translate('Total', getLocale()) }}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="invoice-p">
                    <p class="invoice-p">{{ $payment_history->title }}</p>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right invoice-p currency">
                    {{ currencyExchange($payment_history->primary_amount, true, null, false) }}
                </td>
            </tr>


            <tr>
                <td colspan="3"></td>
                <td colspan="2">
                    <table class="table table-borderless w-100 invoice-summary-table">
                        <tr>
                            <td class="border-top-0 invoice-p p-0">{{ translate('Subtotal', getLocale()) }}</td>
                            <td class="border-top-0 text-right invoice-p p-0 currency">
                                {{ currencyExchange($payment_history->primary_amount, true, null, false) }}</td>
                        </tr>
                        <tr>
                            <td class="border-top-0 invoice-p p-0">{{ translate('Discount', getLocale()) }}</td>
                            <td class="border-top-0 invoice-p text-right p-0 currency">
                                {{ currencyExchange($payment_history->discount_amount, true, null, false) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-top-0 invoice-p p-0">{{ translate('Total', getLocale()) }}</td>
                            <td class="border-top-0 invoice-p text-right p-0 currency">
                                {{ currencyExchange($payment_history->final_amount, true, null, false) }}
                            </td>
                        </tr>
                    </table>
                </td>
            <tr>

        </tbody>
    </table>
</body>

</html>
