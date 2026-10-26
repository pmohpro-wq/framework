<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>درگاه پرداخت — فروشگاه من</title>
    <link rel="stylesheet" href="{{ asset('assets/store/css/store.css') }}">
</head>
<body>

<div class="bank-page">
    <div class="bank-head">
        <div class="bank-logo">بانک</div>
        <div>
            <div style="font-weight:700">درگاه پرداخت اینترنتی</div>
            <div style="font-size:12px;opacity:.7">پرداخت امن — شبیه‌ساز درگاه</div>
        </div>
    </div>

    <div class="bank-body">
        <div class="bank-amount">{{ \App\Support\Persian::toman($payment->amount) }}</div>

        <div class="bank-info">
            <div><span>پذیرنده</span><span>فروشگاه من</span></div>
            <div><span>شناسه پیگیری</span><span dir="ltr">{{ $payment->payment_id }}</span></div>
            <div><span>محصول</span><span dir="ltr">{{ $payment->payable ? $payment->payable->number : '-' }}</span></div>
        </div>

        <form action="{{ route('payment.iranian.callback') }}" method="post" class="bank-actions">
            @csrf
            <input type="hidden" name="payment" value="{{ $payment->payment_id }}">

            <button type="submit" name="status" value="success" class="btn btn-primary btn-lg">
                پرداخت موفق ✔
            </button>
            <button type="submit" name="status" value="failed" class="btn btn-outline btn-lg">
                انصراف / خطا ✖
            </button>
        </form>

        <p class="muted" style="font-size:12px;margin-top:18px;text-align:center">
            این صفحه شبیه‌ساز درگاه بانکی است. برای اتصال به درگاه واقعی (زرین‌پال و مشابه)،
            تنظیمات <code dir="ltr">IRANIAN_GATEWAY_*</code> در فایل env را کامل کنید.
        </p>
    </div>
</div>

</body>
</html>
