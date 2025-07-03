<?php
return [
    'user_type' => [
        'admin' => 1,
        'customer' => 2,
        'seller' => 3,
    ],
    'product_type' => [
        'physical_product' => 1,
    ],
    'product_variant' => [
        'single' => 2,
        'variable' => 1,
    ],
    'amount_type' => [
        'percent' => 1,
        'flat' => 2,
    ],
    'shipping_cost_options' => [
        'flat_rate' => 1,
        'product_wise_rate' => 2,
        'profile_wise_rate' => 3,
        'location_wise_rate' => 4
    ],
    'shipping_location' => [
        'custom' => 'custom',
        'anywhere' => 'anywhere',
    ],
    'cod_location' => [
        'custom' => 'custom',
        'anywhere' => 'anywhere',
    ],
    'shipping_based_on' => [
        'weight_based' => 'weight_based',
        'price_based' => 'price_based',
    ],
    'shipping_rate_type' => [
        'own_rate' => 'own_rate',
        'carrier_rate' => 'carrier_rate',
    ],
    'shipped_by' => [
        'by_air' => 1,
        'by_ship' => 2,
        'by_rail' => 3,
        'by_train' => 4,
    ],
    'time_unit' => [
        'Days' => 'Days',
        'Hours' => 'Hours',
        'Minutes' => 'Minutes'
    ],
    'payment_methods' => [
        'cod' => 1,
        'paypal' => 2,
        'stripe' => 3,
        'paddle' => 4,
        'sslcommerz' => 5,
        'paystack' => 6,
        'razorpay' => 7,
        'mollie' => 8,
        'bank' => 9,
        'gpay' => 10,
        'wipay' => 11,
        'powertranzpay' => 12,
        'avariamoney' => 13,
        'paymob' => 15,
        'mercado-pago' => 16,
        'bkash' => 17
    ],
    'order_type' => [
        'local_pickup' => 1,
        'home_delivery' => 2,
    ],
    'order_payment_status' => [
        'unpaid' => 2,
        'paid' => 1,
    ],
    'order_delivery_status' => [
        'pending' => 2,
        'processing' => 5,
        'ready_to_ship' => 6,
        'shipped' => 3,
        'delivered' => 1,
        'cancelled' => 4,
    ],
    'product_return_status' => [
        'not_available' => 1,
        'available' => 2,
        'returned' => 3,
        'processing' => 4,
        'return_cancel' => 5,
    ],
    'return_request_payment_status' => [
        'pending' => 2,
        'refunded' => 1,
    ],
    'return_request_status' => [
        'approved' => 1,
        'pending' => 2,
        'processing' => 3,
        'cancelled' => 4,
        'product_received' => 5
    ],
    'offline_payment_type' => [
        'bank' => 1,
        'cheque' => 2,
        'custom' => 3
    ],
    'wallet_recharge_type' => [
        'online' => 1,
        'offline' => 2,
        'manual' => 3,
        'cart' => 4,
        'cashback' => 5,
        'refund' => 6,
    ],
    'wallet_entry_type' => [
        'debit' => 1,
        'credit' => 2,
    ],
    'wallet_transaction_status' => [
        'accept' => 1,
        'declined' => 2,
        'pending' => 3
    ],
    'custom_notification_receiver_type' => [
        'all_customers' => 1,
        'specific_customer' => 2,
        'all_users' => 3,
        'specific_user' => 4,
        'specific_user_role' => 5
    ],
    'custom_notification_type' => [
        'email' => 1,
        'dashboard' => 2,
        'email_dashboard' => 3
    ],
    'seller_earning_status' => [
        'approve' => 1,
        'pending' => 2,
        'refunded' => 3
    ],
];
