<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductReview;
use Konekt\AppShell\Http\Controllers\BaseController;

class ReviewAdminController extends BaseController
{
    public function index()
    {
        return view('admin.reviews', [
            'pending' => ProductReview::with('product')->where('is_approved', false)->orderByDesc('created_at')->get(),
            'approved' => ProductReview::with('product')->where('is_approved', true)->orderByDesc('created_at')->take(50)->get(),
        ]);
    }

    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', __('نظر تأیید و منتشر شد.'));
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return back()->with('success', __('نظر حذف شد.'));
    }
}
