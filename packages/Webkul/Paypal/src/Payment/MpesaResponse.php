<?php

namespace Webkul\Paypal\Payment;

class MpesaResponse
{
    protected $success;
    protected $message;

    public function __construct($success, $message = null)
    {
        $this->success = $success;
        $this->message = $message;
    }

    public function isSuccessful()
    {
        return $this->success;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
