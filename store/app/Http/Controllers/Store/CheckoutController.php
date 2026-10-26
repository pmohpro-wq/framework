<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Payment\Gateways\IranianGateway;
use Illuminate\Http\Request;
use Konekt\Address\Models\CountryProxy;
use Vanilo\Cart\Facades\Cart;
use Vanilo\Checkout\Facades\Checkout;
use Vanilo\Checkout\Models\CheckoutState;
use Vanilo\Foundation\Factories\OrderFactory;
use Vanilo\Foundation\Shipping\Method\Eligibility\AvailableShippingMethods;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethodProxy;

class CheckoutController extends Controller
{
    public function show()
    {
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index')->with('warning', 'سبد خرید شما خالی است.');
        }

        Checkout::setCart(Cart::model());

        $shippingMethods = AvailableShippingMethods::forCheckout(Checkout::getFacadeRoot())->get();
        $paymentMethods  = PaymentMethodProxy::query()->where('is_enabled', true)->orderBy('name')->get();
        $iran            = CountryProxy::where('isoalpha2', 'IR')->first();

        return view('store.checkout', [
            'billpayer'       => Checkout::getBillpayer(),
            'shippingMethods' => $shippingMethods,
            'paymentMethods'  => $paymentMethods,
            'iran'            => $iran,
            'total'           => Cart::total(),
        ]);
    }

    public function placeOrder(Request $request)
    {
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index')->with('warning', 'سبد خرید شما خالی است.');
        }

        Checkout::setCart(Cart::model());

        $data = $request->validate([
            'firstname'        => ['required', 'string', 'max:60'],
            'lastname'         => ['required', 'string', 'max:60'],
            'email'            => ['required', 'email', 'max:120'],
            'phone'            => ['required', 'string', 'max:20'],
            'city'             => ['required', 'string', 'max:60'],
            'address'          => ['required', 'string', 'max:255'],
            'postalcode'       => ['nullable', 'string', 'max:12'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'shipping_method'  => ['required', 'integer', 'exists:shipping_methods,id'],
            'payment_method'   => ['required', 'integer', 'exists:payment_methods,id'],
        ]);

        $country = CountryProxy::where('name', 'like', 'Iran%')->first();

        Checkout::update([
            'billpayer' => [
                'firstname' => $data['firstname'],
                'lastname'  => $data['lastname'],
                'email'     => $data['email'],
                'phone'     => $data['phone'],
                'address'   => [
                    'name'       => trim($data['firstname'] . ' ' . $data['lastname']),
                    'country_id' => $country?->id,
                    'city'       => $data['city'],
                    'address'    => $data['address'],
                    'postalcode' => $data['postalcode'] ?? null,
                ],
            ],
            'ship_to_billing_address' => true,
            'shipping_method_id'      => $data['shipping_method'],
            'payment_method_id'       => $data['payment_method'],
            'notes'                   => $data['notes'] ?? null,
        ]);

        Checkout::setState(CheckoutState::READY());

        /** @var \Vanilo\Order\Contracts\Order $order */
        $order = app(OrderFactory::class)->createFromCheckout(Checkout::getFacadeRoot());

        /** @var \Vanilo\Payment\Models\PaymentMethod $paymentMethod */
        $paymentMethod = PaymentMethodProxy::find($order->getPaymentMethodId());

        $payment = PaymentFactory::createFromPayable($order, $paymentMethod);

        // خالی کردن سبد و تسویه‌حساب پس از ثبت سفارش
        Cart::destroy();
        Checkout::clear();

        // هدایت به پرداخت بر اساس درگاه انتخاب‌شده
        if ($paymentMethod->gateway === IranianGateway::ID) {
            return redirect()->to(
                route('payment.iranian.pay', ['payment' => $payment->getPaymentId()])
            );
        }

        // درگاه آفلاین (پرداخت در محل) → صفحه موفقیت
        return redirect()->route('checkout.success', ['orderNumber' => $order->number])
            ->with('success', 'سفارش شما با موفقیت ثبت شد.');
    }

    public function success(string $orderNumber)
    {
        $order = \Vanilo\Order\Models\OrderProxy::where('number', $orderNumber)->firstOrFail();

        return view('store.order_success', ['order' => $order]);
    }
}
