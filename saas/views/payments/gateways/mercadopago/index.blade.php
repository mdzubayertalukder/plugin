<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>

<body>
    <script src="https://sdk.mercadopago.com/js/v2"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            try {
                const mp = new MercadoPago("{{ $public_key }}", {
                    locale: 'en-US'
                });

                mp.checkout({
                    preference: {
                        id: "{{ $init_point }}"
                    },
                    autoOpen: true,
                });
            } catch (error) {
                console.error('Error initializing MercadoPago:', error);
            }
        });
    </script>
</body>

</html>
