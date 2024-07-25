<?php

namespace Webkul\Paypal\Payment;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Webkul\Payment\Payment\Payment;

class Mpesa extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'mpesa';

    /**
     * Return M-Pesa redirect URL.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('mpesa.redirect');
    }

    /**
     * Return M-Pesa IPN URL.
     *
     * @return string
     */
    public function getIPNUrl()
    {
        return $this->getConfigData('sandbox')
            ? 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/transactionstatus'
            : 'https://api.safaricom.co.ke/mpesa/c2b/v1/transactionstatus';
    }

    /**
     * Return form field array.
     *
     * @return array
     */
    public function getFormFields()
    {
        $cart = $this->getCart();

        $fields = [
            'BusinessShortCode' => $this->getConfigData('business_short_code'),
            'Password'          => $this->generatePassword(),
            'Timestamp'         => now()->format('YmdHis'),
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $cart->grand_total,
            'PartyA'            => $this->getCustomerPhoneNumber(),
            'PartyB'            => $this->getConfigData('business_short_code'),
            'PhoneNumber'       => $this->getCustomerPhoneNumber(),
            'CallBackURL'       => route('mpesa.ipn'),
            'AccountReference'  => $cart->id,
            'TransactionDesc'   => 'Payment for Order ' . $cart->id,
        ];

        return $fields;
    }

    /**
     * Generate password for M-Pesa transaction.
     *
     * @return string
     */
    protected function generatePassword()
    {
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->getConfigData('business_short_code') . $this->getConfigData('passkey') . $timestamp);

        return $password;
    }

    /**
     * Get customer phone number.
     *
     * @return string
     */
    protected function getCustomerPhoneNumber()
    {
        $cart = $this->getCart();
        return $cart->billing_address->phone; // Adjust this if phone number is stored differently
    }

    /**
     * Initiate M-Pesa payment using STK Push.
     *
     * @param array $formData
     * @return object
     */
    public function initiatePayment(array $formData)
    {
        // Log the form data for debugging
        Log::info('Initiating M-Pesa Payment', $formData);

        try {
            $client = new Client();
            $response = $client->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'BusinessShortCode' => $formData['BusinessShortCode'],
                    'Password' => $formData['Password'],
                    'Timestamp' => $formData['Timestamp'],
                    'TransactionType' => $formData['TransactionType'],
                    'Amount' => $formData['Amount'],
                    'PartyA' => $formData['PartyA'],
                    'PartyB' => $formData['PartyB'],
                    'PhoneNumber' => $formData['PhoneNumber'],
                    'CallBackURL' => $formData['CallBackURL'],
                    'AccountReference' => $formData['AccountReference'],
                    'TransactionDesc' => $formData['TransactionDesc']
                ]
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            Log::info('M-Pesa Payment Response', $responseBody);

            // Check if the payment was successfully initiated
            if (isset($responseBody['ResponseCode']) && $responseBody['ResponseCode'] == '0') {
                return (object) [
                    'status' => 'success',
                    'response' => $responseBody
                ];
            } else {
                return (object) [
                    'status' => 'failure',
                    'response' => $responseBody
                ];
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa Payment Error', ['message' => $e->getMessage()]);
            return (object) [
                'status' => 'error',
                'response' => $e->getMessage()
            ];
        }
    }

    /**
     * Get M-Pesa access token.
     *
     * @return string
     */
    protected function getAccessToken()
    {
        // Implement logic to retrieve the access token from M-Pesa API
        // This is just an example; replace with actual logic to obtain access token
        return 'your_access_token';
    }
}
