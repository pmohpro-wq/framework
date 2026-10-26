<?php

declare(strict_types=1);

namespace App\Payment\Gateways;

use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentRequest;

/**
 * درخواست پرداخت درگاه ایرانی.
 * در حالت شبیه‌ساز، کاربر به صفحه پرداخت داخلی هدایت می‌شود؛
 * در اتصال واقعی، می‌توان HTML فرم خودکار (auto-submit) بانک را برگرداند.
 */
class IranianPaymentRequest implements PaymentRequest
{
    public function __construct(
        protected Payment $payment,
        protected array $options = [],
    ) {
    }

    /** HTML فرم ارسال به درگاه (در حالت واقعی استفاده می‌شود) */
    public function getHtmlSnippet(array $options = []): ?string
    {
        return null;
    }

    public function willRedirect(): bool
    {
        return true;
    }

    public function getRemoteId(): ?string
    {
        return $this->options['authority'] ?? null;
    }

    /** آدرس صفحه پرداخت (شبیه‌ساز داخلی یا آدرس بانک در اتصال واقعی) */
    public function getRedirectUrl(): string
    {
        return $this->options['redirect_url']
            ?? route('payment.iranian.pay', ['paymentHash' => $this->payment->getPaymentId()]);
    }
}
