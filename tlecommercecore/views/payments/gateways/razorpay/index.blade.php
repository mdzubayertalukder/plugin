<!DOCTYPE html>
<html>

<head>
    <title>Razorpay Payment</title>
    <style>
        body {
            background-color: #F7F8FA;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .payment-status {
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
            border: 1px solid #F7F8FA;
            box-shadow: 3px 3px 30px rgba(0, 0, 0, 0.03);
            min-width: 50%;
            text-align: center;
        }

        .details-status {
            padding: 30px;
        }

        .payment-status h1 {
            margin: 0;
            font-size: 24px;
            padding-block: 10px;
            font-weight: 700;
            background: #EF2543;
            color: #fff;
            text-align: center;
        }

        .payment-status p {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #3B3B3B;
        }

        .payment-status p:not(:last-child) {
            margin-bottom: 10px;
        }

        .payment-status .razorpay-payment-button {
            align-items: center;
            border: none;
            border-radius: 0;
            cursor: pointer;
            display: inline-flex;
            font-size: 16px;
            font-weight: 700;
            padding: 13px 35px;
            text-transform: capitalize;
            background: #EF2543;
            color: #fff;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="payment-status">
        <h1>Payment Details</h1>
        <div class="details-status">
            <p>Amount: {{ $order['amount'] }}</p>
            <form action="{{ route('razorpay.payment.submit') }}" method="POST" id="pay_with_razorpay">
                @csrf
                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ $key_id }}"
                    data-amount="{{ $order['amount'] }}" data-currency="{{ $order['currency'] }}" data-order-id="{{ $order['id'] }}"
                    data-buttontext="Pay with Razorpay" data-description="{{ $order['id'] }}"></script>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#pay_with_razorpay').submit();
        });
    </script>
</body>

</html>
