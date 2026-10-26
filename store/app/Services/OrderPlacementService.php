<?php

namespace App\Services;

use App\Http\Requests\Store\PlaceOrderRequest;
use App\Mail\OrderPlaced;
use App\Models\User;
use App\Services\Exceptions\InvalidCouponException;
use App\Services\Exceptions\StockShortageException;
use App\Support\StockManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Konekt\Address\Models\CountryProxy;
use Vanilo\Cart\Facades\Cart;
use Vanilo\Checkout\Facades\Checkout;
use Vanilo\Checkout\Models\CheckoutState;
use Vanilo\Foundation\Factories\OrderFactory;
use Vanilo\Foundation\Models\Customer;
use Vanilo\Order\Contracts\Order;
use Vanilo\Payment\Factories\PaymentFactory;
use Vanilo\Payment\Models\PaymentMethodProxy;
use Vanilo\Promotion\Models\CouponProxy;

/**
 * ثبت کامل یک سفارش از روی سبد خرید و فرم تسویه‌حساب.
 *
 * ترتیب گام‌ها (حیاتی است، تغییرش با احتیاط):
 *   ۱. کنترل موجودی انبار → ۲. اعتبارسنجی کوپن → ۳. به‌روزرسانی Checkout →
 *   ۴. ساخت سفارش از Checkout → ۵. اتصال مشتری → ۶. ساخت Payment →
 *   ۷. کسر موجودی → ۸. شمارش کوپن → ۹. ایمیل تأیید → ۱۰. خالی‌کردن سبد.
 *
 * خرابی‌های قابل پیش‌بینی (موجودی، کوپن) به‌صورت Exception تایپ‌دار
 * بالا می‌روند؛ خرابی ایمیل ثبت سفارش را بی‌اثر نمی‌کند.
 */
class OrderPlacementService
{
    public function place(PlaceOrderRequest $request): PlacementResult
    {
        // ۱) کنترل موجودی انبار پیش از ثبت سفارش
        if ($shortage = StockManager::findShortages(Cart::getItems())) {
            throw new StockShortageException(
                collect($shortage)->pluck('name')->all()
            );
        }

        Checkout::setCart(Cart::model());

        $data = $request->validated();

        // ۲) اعتبارسنجی کد تخفیف (پیش از ثبت سفارش، با پیام واضح)
        $coupon = null;

        if (! empty($data['coupon'])) {
            $code   = strtoupper(trim($data['coupon']));
            $coupon = CouponProxy::findByCode($code);

            if (! $coupon || ! $coupon->canBeUsed()) {
                throw new InvalidCouponException($code);
            }

            Checkout::addCoupon($code);
        }

        // ۳) به‌روزرسانی وضعیت تسویه‌حساب
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

        // ۴) ساخت سفارش
        /** @var Order $order */
        $order = app(OrderFactory::class)->createFromCheckout(Checkout::getFacadeRoot());

        // ۵) اتصال سفارش به کاربر/مشتری (حساب کاربری + پنل مدیریت)
        $this->attachCustomer($order, $data, $request->user());

        // ۶) ساخت پرداخت
        /** @var \Vanilo\Payment\Models\PaymentMethod $paymentMethod */
        $paymentMethod = PaymentMethodProxy::find($order->getPaymentMethodId());
        $payment       = PaymentFactory::createFromPayable($order, $paymentMethod);

        // ۷) کسر از موجودی انبار
        StockManager::deductForCartItems(Cart::getItems());

        // ۸) شمارش استفاده از کوپن
        if ($coupon) {
            $coupon->update(['usage_count' => $coupon->usage_count + 1]);
        }

        // ۹) ایمیل تأیید سفارش (خرابی ایمیل، ثبت سفارش را بی‌اثر نمی‌کند)
        $this->sendConfirmationEmail($order, $data['email']);

        // ۱۰) خالی کردن سبد و تسویه‌حساب
        Cart::destroy();
        Checkout::clear();

        return new PlacementResult($order, $paymentMethod, $payment);
    }

    // ------------------------------------------------------------------

    /**
     * اتصال سفارش به کاربر واردشده؛ برای مهمان‌ها هم پروفایل مشتری ساخته/یافته
     * می‌شود تا پنل مدیریت فهرست کامل مشتریان را داشته باشد.
     */
    private function attachCustomer(Order $order, array $data, ?User $user): void
    {
        if ($user) {
            $customer = $user->customer;

            if (! $customer) {
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

        if (! $customer) {
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

    private function sendConfirmationEmail(Order $order, string $email): void
    {
        try {
            Mail::to($email)->send(new OrderPlaced($order));
        } catch (\Throwable $e) {
            Log::warning('Order email failed: ' . $e->getMessage());
        }
    }
}
