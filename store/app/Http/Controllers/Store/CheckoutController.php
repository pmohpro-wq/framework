<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\OrderPlaced;
use App\Payment\Gateways\IranianGateway;
use App\Support\StockManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Konekt\Address\Models\CountryProxy;
use Vanilo\Cart\Facades\Cart;
use Vanilo\Checkout\Facades\Checkout;
use Vanilo\Checkout\Models\CheckoutState;
use Vanilo\Foundation\Factories\OrderFactory;
use Vanilo\Foundation\Models\Customer;
use Vanilo\Foundation\Shipping\Method\Eligibility\AvailableShippingMethods;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethodProxy;
use Vanilo\Product\Models\ProductProxy;
use Vanilo\Promotion\Models\CouponProxy;

class CheckoutController extends Controller
{
    public function show()
    {
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index')->with('warning', 'سبد خرید شما خالی است.');
        }

        Checkout::setCart(Cart::model());

        // پیش‌پر کردن اطلاعات خریدار برای کاربر واردشده
        $user = Auth::user();
        if ($user) {
            $customer = $user->customer;
            $nameParts = explode(' ', (string) $user->name, 2);

            $billpayer = Checkout::getBillpayer();
            $billpayer->firstname = $customer->firstname ?? $nameParts[0] ?? '';
            $billpayer->lastname  = $customer->lastname ?? ($nameParts[1] ?? '');
            $billpayer->email     = $user->email;
            $billpayer->phone     = $customer->phone ?? '';
            Checkout::setBillpayer($billpayer);
        }

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

        // ۱) بررسی موجودی انبار پیش از ثبت سفارش
        if ($shortage = StockManager::findShortages(Cart::getItems())) {
            $names = collect($shortage)->pluck('name')->implode('، ');

            return redirect()
                ->route('cart.index')
                ->with('warning', "موجودی کالاهای زیر کافی نیست: {$names}. لطفاً سبد خرید را اصلاح کنید.");
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
            'coupon'           => ['nullable', 'string', 'max:40'],
            'shipping_method'  => ['required', 'integer', 'exists:shipping_methods,id'],
            'payment_method'   => ['required', 'integer', 'exists:payment_methods,id'],
        ]);

        // ۲) اعتبارسنجی کد تخفیف (پیش از ثبت سفارش، با پیام واضح)
        $coupon = null;
        if (!empty($data['coupon'])) {
            $code = strtoupper(trim($data['coupon']));
            $coupon = CouponProxy::findByCode($code);

            if (!$coupon || !$coupon->canBeUsed()) {
                return back()
                    ->withInput()
                    ->withErrors(['coupon' => 'کد تخفیف معتبر نیست یا منقضی شده است.']);
            }

            Checkout::setCart(Cart::model());
            Checkout::addCoupon($code);
        }

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

        // ۳) اتصال سفارش به کاربر/مشتری (برای تاریخچهٔ حساب کاربری و پنل مدیریت)
        $this->attachCustomer($order, $data);

        /** @var \Vanilo\Payment\Models\PaymentMethod $paymentMethod */
        $paymentMethod = PaymentMethodProxy::find($order->getPaymentMethodId());

        $payment = PaymentFactory::createFromPayable($order, $paymentMethod);

        // ۴) کسر از موجودی انبار + شمارش فروش
        StockManager::deductForCartItems(Cart::getItems());

        // ۵) شمارش استفاده از کوپن
        if ($coupon) {
            $coupon->update(['usage_count' => $coupon->usage_count + 1]);
        }

        // ۶) ایمیل تأیید سفارش (اگر ارسال ایمیل خراب شود، ثبت سفارش بی‌اثر نمی‌ماند)
        try {
            Mail::to($data['email'])->send(new OrderPlaced($order));
        } catch (\Throwable $e) {
            Log::warning('Order email failed: ' . $e->getMessage());
        }

        // خالی کردن سبد و تسویه‌حساب پس از ثبت سفارش
        Cart::destroy();
        Checkout::clear();

        // هدایت به پرداخت بر اساس درگاه انتخاب‌شده
        $isOnlineGateway = in_array($paymentMethod->gateway, [IranianGateway::ID, 'zarinpal'], true);

        if ($isOnlineGateway) {
            // درخواست پرداخت از درگاه می‌گیرد (شبیه‌ساز داخلی یا صفحهٔ بانک در اتصال واقعی)
            $paymentRequest = $paymentMethod->getGateway()->createPaymentRequest($payment);

            return redirect()->away($paymentRequest->getRedirectUrl());
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

    // ------------------------------------------------------------------

    /**
     * اتصال سفارش به کاربر واردشده؛ برای مهمان‌ها هم پروفایل مشتری ساخته/یافته می‌شود
     * تا پنل مدیریت فهرست کامل مشتریان را داشته باشد.
     */
    private function attachCustomer($order, array $data): void
    {
        $user = Auth::user();

        if ($user) {
            $customer = $user->customer;

            if (!$customer) {
                $customer = Customer::create([
                    'type'      => 'individual',
                    'firstname' => $data['firstname'],
                    'lastname'  => $data['lastname'],
                    'email'     => $data['email'],
                    'phone'     => $data['phone'],
                ]);
                $user->update(['customer_id' => $customer->id]);
            }

            $order->update([
                'user_id'     => $user->id,
                'customer_id' => $customer->id,
            ]);

            return;
        }

        // مهمان: ساخت/یافتن مشتری بر اساس ایمیل
        $customer = Customer::where('email', $data['email'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'type'      => 'individual',
                'firstname' => $data['firstname'],
                'lastname'  => $data['lastname'],
                'email'     => $data['email'],
                'phone'     => $data['phone'],
            ]);
        }

        $order->update(['customer_id' => $customer->id]);
    }
}
