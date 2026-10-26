<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\PlaceOrderRequest;
use App\Payment\Gateways\IranianGateway;
use App\Services\Exceptions\InvalidCouponException;
use App\Services\Exceptions\StockShortageException;
use App\Services\OrderPlacementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Konekt\Address\Models\CountryProxy;
use Vanilo\Cart\Facades\Cart;
use Vanilo\Checkout\Facades\Checkout;
use Vanilo\Foundation\Shipping\Method\Eligibility\AvailableShippingMethods;
use Vanilo\Order\Models\OrderProxy;
use Vanilo\Payment\Models\PaymentMethodProxy;

/**
 * نمایش فرم تسویه‌حساب و هدایت پس از ثبت سفارش.
 *
 * منطق ثبت سفارش در App\Services\OrderPlacementService است؛
 * این کنترلر فقط HTTP را ترجمه می‌کند (redirect/error/flash).
 */
class CheckoutController extends Controller
{
    public function __construct(private readonly OrderPlacementService $placement)
    {
    }

    public function show()
    {
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index')->with('warning', 'سبد خرید شما خالی است.');
        }

        Checkout::setCart(Cart::model());
        $this->prefillBillpayer();

        return view('store.checkout', [
            'billpayer'       => Checkout::getBillpayer(),
            'shippingMethods' => AvailableShippingMethods::forCheckout(Checkout::getFacadeRoot())->get(),
            'paymentMethods'  => PaymentMethodProxy::query()->where('is_enabled', true)->orderBy('name')->get(),
            'iran'            => CountryProxy::where('isoalpha2', 'IR')->first(),
            'total'           => Cart::total(),
        ]);
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index')->with('warning', 'سبد خرید شما خالی است.');
        }

        try {
            $result = $this->placement->place($request);
        } catch (StockShortageException $e) {
            $names = implode('، ', $e->productNames);

            return redirect()
                ->route('cart.index')
                ->with('warning', "موجودی کالاهای زیر کافی نیست: {$names}. لطفاً سبد خرید را اصلاح کنید.");
        } catch (InvalidCouponException) {
            return back()
                ->withInput()
                ->withErrors(['coupon' => 'کد تخفیف معتبر نیست یا منقضی شده است.']);
        }

        // درگاه آنلاین (شبیه‌ساز داخلی / زرین‌پال) → هدایت به صفحهٔ پرداخت
        if (in_array($result->paymentMethod->gateway, [IranianGateway::ID, 'zarinpal'], true)) {
            $paymentRequest = $result->paymentMethod->getGateway()->createPaymentRequest($result->payment);

            return redirect()->away($paymentRequest->getRedirectUrl());
        }

        // درگاه آفلاین (پرداخت در محل) → صفحهٔ موفقیت
        return redirect()
            ->route('checkout.success', ['orderNumber' => $result->order->number])
            ->with('success', 'سفارش شما با موفقیت ثبت شد.');
    }

    public function success(string $orderNumber)
    {
        $order = OrderProxy::where('number', $orderNumber)->firstOrFail();

        return view('store.order_success', ['order' => $order]);
    }

    // ------------------------------------------------------------------

    /** پیش‌پر کردن اطلاعات خریدار برای کاربر واردشده */
    private function prefillBillpayer(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $customer  = $user->customer;
        $nameParts = explode(' ', (string) $user->name, 2);

        $billpayer           = Checkout::getBillpayer();
        $billpayer->firstname = $customer->firstname ?? $nameParts[0] ?? '';
        $billpayer->lastname  = $customer->lastname ?? ($nameParts[1] ?? '');
        $billpayer->email     = $user->email;
        $billpayer->phone     = $customer->phone ?? '';

        Checkout::setBillpayer($billpayer);
    }
}
