<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        /* Set the iframe to cover the full viewport */
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        iframe {
            width: 100vw;
            height: 100vh;
            border: none;
        }
    </style>
</head>

<body>
    <iframe src="https://accept.paymob.com/api/acceptance/iframes/{{ $iframe_key }}?payment_token={{ $payment_key }}"
        width="100%" height="600">
    </iframe>
</body>

</html>
