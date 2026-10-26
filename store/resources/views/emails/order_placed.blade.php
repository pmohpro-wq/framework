<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Vazirmatn,Tahoma,'Segoe UI',sans-serif;direction:rtl;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;">
                    {{-- سربرگ --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#ef4056,#b8324a);padding:28px;text-align:center;">
                            <span style="display:inline-block;width:44px;height:44px;line-height:44px;background:#ffffff;border-radius:12px;color:#ef4056;font-size:24px;font-weight:bold;">ف</span>
                            <div style="color:#ffffff;font-size:20px;font-weight:bold;margin-top:10px;">فروشگاه من</div>
                            <div style="color:rgba(255,255,255,.85);font-size:13px;margin-top:4px;">سفارش شما ثبت شد</div>
                        </td>
                    </tr>

                    {{-- بدنه --}}
                    <tr>
                        <td style="padding:28px;">
                            <p style="font-size:15px;color:#262626;margin:0 0 8px;">
                                سلام {{ ($order->billpayer->firstname ?? '') . ' ' . ($order->billpayer->lastname ?? '') }} عزیز،
                            </p>
                            <p style="font-size:14px;color:#555;line-height:2;margin:0 0 20px;">
                                سفارش شما با موفقیت ثبت شد و پس از تأیید پرداخت آماده‌سازی خواهد شد. جزئیات سفارش در ادامه آمده است.
                            </p>

                            {{-- شماره سفارش --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;border-radius:10px;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:14px 18px;font-size:14px;color:#262626;">
                                        شمارهٔ سفارش: <b dir="ltr">{{ $order->number }}</b>
                                    </td>
                                    <td style="padding:14px 18px;font-size:14px;color:#262626;text-align:left;">
                                        وضعیت: <b>در حال پردازش</b>
                                    </td>
                                </tr>
                            </table>

                            {{-- اقلام سفارش --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eceff3;border-radius:10px;overflow:hidden;">
                                <tr style="background:#f8f9fa;font-size:13px;color:#888;">
                                    <td style="padding:10px 14px;">کالا</td>
                                    <td style="padding:10px 14px;text-align:center;">تعداد</td>
                                    <td style="padding:10px 14px;text-align:left;">مبلغ</td>
                                </tr>
                                @foreach($order->items as $item)
                                    <tr style="border-top:1px solid #eceff3;font-size:14px;color:#262626;">
                                        <td style="padding:10px 14px;">{{ $item->name }}</td>
                                        <td style="padding:10px 14px;text-align:center;">{{ \App\Support\Persian::digits($item->qty) }}</td>
                                        <td style="padding:10px 14px;text-align:left;" dir="rtl">{{ \App\Support\Persian::toman($item->total) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="border-top:2px solid #eceff3;background:#fff8f9;font-size:15px;font-weight:bold;color:#ef4056;">
                                    <td style="padding:12px 14px;" colspan="2">جمع کل سفارش</td>
                                    <td style="padding:12px 14px;text-align:left;" dir="rtl">{{ \App\Support\Persian::toman($order->total()) }}</td>
                                </tr>
                            </table>

                            {{-- پیگیری --}}
                            <div style="text-align:center;margin-top:24px;">
                                <a href="{{ url('/track?order_number=' . $order->number) }}"
                                   style="display:inline-block;background:#ef4056;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;font-size:14px;font-weight:bold;">
                                    پیگیری سفارش
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- پاورقی --}}
                    <tr>
                        <td style="background:#f8f9fa;padding:16px;text-align:center;color:#999;font-size:12px;line-height:2;">
                            این ایمیل به‌صورت خودکار ارسال شده است؛ لطفاً به آن پاسخ ندهید.<br>
                            فروشگاه من — خریدی مطمئن، ساده و سریع
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
