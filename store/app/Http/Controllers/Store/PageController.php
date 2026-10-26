<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Support\StoreContent;
use Illuminate\Http\Request;

/**
 * صفحات ایستای فروشگاه. متن‌ها در App\Support\StoreContent نگهداری می‌شوند.
 */
class PageController extends Controller
{
    public function about()
    {
        return $this->page('درباره ما', 'فروشگاه من؛ خریدی مطمئن، ساده و سریع', StoreContent::aboutSections());
    }

    public function terms()
    {
        return $this->page('قوانین و مقررات', 'شرایط استفاده از فروشگاه و ثبت سفارش', StoreContent::termsSections());
    }

    public function faq()
    {
        return view('store.faq', ['faqs' => StoreContent::faqItems()]);
    }

    public function shipping()
    {
        return $this->page('راهنمای خرید و ارسال', 'مراحل خرید، روش‌های ارسال و هزینه‌ها', StoreContent::shippingSections());
    }

    public function returns()
    {
        return $this->page('شرایط بازگشت کالا', '۷ روز فرصت بازگشت بدون قید و شرط', StoreContent::returnsSections());
    }

    public function contact()
    {
        return view('store.contact');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:80'],
            'email'   => ['required', 'email', 'max:120'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'message.min' => 'متن پیام باید حداقل ۱۰ کاراکتر باشد.',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'پیام شما ثبت شد. کارشناسان ما حداکثر تا یک روز کاری پاسخ می‌دهند.');
    }

    // ------------------------------------------------------------------

    private function page(string $title, string $lead, array $sections)
    {
        return view('store.page', [
            'title'    => $title,
            'lead'     => $lead,
            'sections' => $sections,
        ]);
    }
}
