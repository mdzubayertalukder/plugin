<!DOCTYPE html>
<html>

<head>
    <title>{{ translate('Stripe Payment') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- ======= BEGIN GLOBAL MANDATORY STYLES ======= -->
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/backend/assets/fonts/icofont/icofont.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/public/backend/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.css') }}">
    <!-- ======= END BEGIN GLOBAL MANDATORY STYLES ======= -->
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .preloader {
            border: 10px solid rgb(28, 166, 76);
            border-radius: 50%;
            border-top: 12px solid rgb(0, 45, 121);
            width: 100px;
            height: 100px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
            margin: auto;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="mn-vh-100 d-flex align-items-center mx-1350">
        <div class="container-fluid">
            <div class="border-0 card justify-content-center px-4 py-5 text-center">
                <p>{{ translate('Don not close the tab. The payment is being processed') }} . . .</p>
                <button id="checkout-button" style="display: none;"></button>
                <div class="preloader"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
        var stripe = Stripe('{{ $stripe_public_key }}');
        var checkoutButton = document.getElementById('checkout-button');

        checkoutButton.addEventListener('click', function() {
            fetch('{{ route('stripe.generate.token') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        "X-CSRF-Token": csrfToken
                    }
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(session) {
                    return stripe.redirectToCheckout({
                        sessionId: session.id
                    });
                })
                .then(function(result) {
                    if (result.error) {
                        alert(result.error.message);
                        location.href = '/';
                    }
                })
                .catch(function(error) {
                    alert('Something went wrong. Please try again');
                    location.href = '/';
                });
        });
        document.getElementById("checkout-button").click();
    </script>
</body>

</html>
