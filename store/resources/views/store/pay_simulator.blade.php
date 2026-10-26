<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>درگاه پرداخت — فروشگاه من</title>
    <link rel="stylesheet" href="/assets/store/css/store.css">
</head>
<body>

<div class="bank-page">
    <div class="bank-head">
        <div class="bank-logo">بانک</div>
        <div>
            <div class="title">درگاه پرداخت اینترنتی</div>
            <div class="sub">پرداخت امن — شبیه‌ساز درگاه</div>
        </div>
    </div>

    <div class="bank-body">
        <div class="bank-amount">
            <span class="label">مبلغ قابل پرداخت</span>
            <b>{{ \App\Support\Persian::toman($payment->amount) }}</b>
            <span class="unit">تومان</span>
        </div>

        <div class="bank-info">
            <div><span>پذیرنده</span><span>فروشگاه من</span></div>
            <div><span>شناسه پیگیری</span><span dir="ltr">{{ $payment->getPaymentId() }}</span></div>
            <div><span>محصول</span><span dir="ltr">{{ $payment->payable ? $payment->payable->number : '-' }}</span></div>
        </div>

        <form action="{{ route('payment.iranian.callback') }}" method="post" class="bank-actions">
            @csrf
            <input type="hidden" name="payment" value="{{ $payment->getPaymentId() }}">

            <button type="submit" name="status" value="success" class="btn btn-primary btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                پرداخت موفق
            </button>
            <button type="submit" name="status" value="failed" class="btn btn-outline btn-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                انصراف / خطا
            </button>
        </form>

        <p class="fine-print">
            این صفحه شبیه‌ساز درگاه بانکی است. برای اتصال به درگاه واقعی (زرین‌پال و مشابه)،
            تنظیمات <code dir="ltr">IRANIAN_GATEWAY_*</code> در فایل env را کامل کنید.
        </p>
    </div>
</div>

</body>
</html>
