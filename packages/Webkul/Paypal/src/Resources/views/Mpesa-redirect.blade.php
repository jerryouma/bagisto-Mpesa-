<?php $paypalStandard = app('Webkul\Paypal\Payment\Mpesa') ?>


<body>
    You will be redirected to the M-Pesa website in a few seconds.

    <form action="{{ $mpesa->getRedirectUrl() }}" id="mpesa_checkout" method="POST">
        @csrf
        <input value="Click here if you are not redirected within 10 seconds..." type="submit">

        @foreach ($mpesa->getFormFields() as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
        @endforeach
    </form>

    <script type="text/javascript">
        document.getElementById("mpesa_checkout").submit();
    </script>
</body>
