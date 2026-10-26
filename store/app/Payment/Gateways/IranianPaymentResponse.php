<?php

declare(strict_types=1);

namespace App\Payment\Gateways;

use Illuminate\Http\Request;
use Konekt\Enum\Enum;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Models\PaymentStatus;

/**
 * پاسخ پرداخت درگاه ایرانی.
 *
 * پارامترهای بازگشتی شبیه‌ساز:
 *  - payment : شناسه پرداخت (paymentId)
 *  - status  : success | failed
 *  - ref     : شماره پیگیری
 */
class IranianPaymentResponse implements PaymentResponse
{
    protected PaymentStatus $status;
    protected bool $successful;

    public function __construct(protected Request $request)
    {
        $this->successful = $request->input('status') === 'success';
        $this->status = $this->successful
            ? PaymentStatus::PAID()
            : PaymentStatus::DECLINED();
    }

    public function wasSuccessful(): bool
    {
        return $this->successful;
    }

    public function getMessage(): ?string
    {
        return $this->successful
            ? __('پرداخت با موفقیت انجام شد')
            : __('پرداخت ناموفق بود یا لغو شد');
    }

    public function getTransactionId(): ?string
    {
        return $this->request->input('ref');
    }

    public function getAmountPaid(): ?float
    {
        return $this->successful ? $this->getTransactionAmount() : null;
    }

    public function getTransactionAmount(): float
    {
        return (float) ($this->request->input('amount') ?? 0);
    }

    public function getPaymentId(): string
    {
        return (string) $this->request->input('payment');
    }

    public function getStatus(): PaymentStatus
    {
        return $this->status;
    }

    public function getNativeStatus(): Enum
    {
        return $this->status;
    }
}
