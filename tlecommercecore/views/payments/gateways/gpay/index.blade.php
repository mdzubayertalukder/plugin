<!DOCTYPE html>
<html>

<head>
    <title>Google Pay</title>
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
            <p>Amount: {{ $currency }} {{ $total_payable_amount }}</p>
            <form action="{{ route('googlepay.payment.submit') }}" method="POST" id="pay_with_gpay">
                @csrf
                <input type="hidden" name="total_payable_amount" id="total_payable_amount" value="{{ $total_payable_amount }}">
                <input type="hidden" name="currency" id="currency" value="{{ $currency }}">
                <input type="hidden" name="marchant_id" id="marchant_id" value="{{ $marchant_id }}">
                <input type="hidden" name="marchant_name" id="marchant_name" value="{{ $marchant_name }}">
                <input type="hidden" name="mode" id="mode" value="{{ $mode }}">
                <input type="hidden" name="payment_status" id="payment_status" value="0">
                <button type="button" id="googlePayButton">Pay With Google Pay</button>
            </form>
        </div>
    </div>
      <script src="https://pay.google.com/gp/p/js/pay.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  
  
    $(document).ready(function() {
        let total_payable_amount = $('#total_payable_amount').val()
        let currency = $('#currency').val()
        let marchant_id = $('#marchant_id').val()
        let marchant_name = $('#marchant_name').val()
        let mode = $('#mode').val()
        
        const paymentDataRequest = {
          apiVersion: 2,
          apiVersionMinor: 0,
          allowedPaymentMethods: [{
            type: 'CARD',
            parameters: {
              allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
              allowedCardNetworks: ["AMEX", "DISCOVER", "JCB", "MASTERCARD", "VISA"]
            },
            tokenizationSpecification: {
              type: 'PAYMENT_GATEWAY',
              parameters: {
                gateway: 'allpayments',
                gatewayMerchantId: marchant_id,
              },
            },
          }],
          merchantInfo: {
            merchantName: marchant_name,
          },
          transactionInfo: {
            totalPriceStatus: 'FINAL',
            totalPrice: total_payable_amount,
            currencyCode: currency
          },
        };
    
        const button = document.getElementById('googlePayButton');
        button.addEventListener('click', () => {
          const paymentsClient = new google.payments.api.PaymentsClient({
            environment: mode,
          });
    
          paymentsClient.loadPaymentData(paymentDataRequest).then(paymentData => {
              $('#payment_status').val(1)
              $('#pay_with_gpay').submit();
          })
          .catch(error => {
            $('#payment_status').val(0)
            $('#pay_with_gpay').submit();
            console.error('Error loading Google Pay:', error);
          });
        });
    });
  </script>
</body>

</html>