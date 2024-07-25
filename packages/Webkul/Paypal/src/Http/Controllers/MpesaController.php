<?php

namespace Webkul\Paypal\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Paypal\Payment\Mpesa;
use Webkul\Paypal\Helpers\Ipn;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected $orderRepository;
    protected $ipnHelper;
    protected $mpesa;

    public function __construct(OrderRepository $orderRepository, Ipn $ipnHelper, Mpesa $mpesa)
    {
        $this->orderRepository = $orderRepository;
        $this->ipnHelper = $ipnHelper;
        $this->mpesa = $mpesa;
    }

    public function redirect(Request $request)
    {
        if ($request->isMethod('post')) {
            // Retrieve form data
            $formData = $request->all();

            // Initiate M-Pesa payment process with the retrieved data
            $response = $this->mpesa->initiatePayment($formData);

            // Handle the response, e.g., redirect to a success or failure page
            if ($response->status == 'success') {
                return redirect()->route('mpesa.success');
            } else {
                return redirect()->route('mpesa.cancel');
            }
        } else {
            // Handle GET request logic here
            return view('paypal::Mpesa-redirect', ['mpesa' => $this->mpesa]);
        }
    }

    public function success()
    {
        $cart = Cart::getCart();
        $data = (new OrderResource($cart))->jsonSerialize();
        $order = $this->orderRepository->create($data);

        // Log the created order for debugging
        Log::info('Success Method - Created Order:', $order);

        Cart::deActivateCart();

        // Redirect to a success page or show success message
        return redirect()->route('shop.checkout.success', ['order_id' => $order->id]);
    }

    public function cancel()
    {
        // Handle cancel logic here, e.g., show a cancellation message
        return redirect()->route('shop.checkout.cart.index')->with('error', 'Payment was cancelled.');
    }

    public function ipn(Request $request)
    {
        // Handle IPN (Instant Payment Notification) logic here
        $this->ipnHelper->processIpn($request->all());

        // Log the IPN request data for debugging
        Log::info('IPN Method - IPN Data:', $request->all());

        return response()->json(['status' => 'success']);
    }
}
