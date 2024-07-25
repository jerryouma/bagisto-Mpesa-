<?php

return [
    'paypal_smart_button' => [
        'code'             => 'paypal_smart_button',
        'title'            => 'PayPal Smart Button',
        'description'      => 'PayPal',
        'client_id'        => 'sb',
        'class'            => 'Webkul\Paypal\Payment\SmartButton',
        'sandbox'          => true,
        'active'           => true,
        'sort'             => 0,
    ],

    'paypal_standard' => [
        'code'             => 'paypal_standard',
        'title'            => 'PayPal Standard',
        'description'      => 'PayPal Standard',
        'class'            => 'Webkul\Paypal\Payment\Standard',
        'sandbox'          => true,
        'active'           => true,
        'business_account' => 'test@webkul.com',
        'sort'             => 3,
    ],
    

    'mpesa' => [
    'code' => 'mpesa',
    'title' => 'M-Pesa',
    'description' => 'M-Pesa Payment Gateway',
    'class' => 'Webkul\Paypal\Payment\Mpesa', // Ensure this matches your namespace and class structure
    'sandbox' => true, // Set to true for testing in sandbox mode
    'active' => true, // Set to true to activate this payment method
    'sort' => 3, // Adjust sorting order if needed
    'business_short_code' => env('MPESA_BUSINESS_SHORTCODE'), // Replace with your M-Pesa business short code
    'passkey' => env('MPESA_PASSKEY'), // Replace with your M-Pesa passkey
    'callback_url' => env('MPESA_CALLBACK_URL'), // Replace with your M-Pesa callback URL
    'api_key' => env('MPESA_API_KEY'), // Replace with your M-Pesa API key
    // Add other necessary configuration keys here
],



];
