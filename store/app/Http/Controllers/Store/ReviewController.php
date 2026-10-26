<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Vanilo\Product\Models\ProductProxy;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $product = ProductProxy::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:80'],
            'email'   => ['nullable', 'email', 'max:120'],
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'comment.min' => 'متن نظر خیلی کوتاه است.',
        ]);

        ProductReview::create([
            'product_id' => $product->id,
            'name'       => $data['name'],
            'email'      => $data['email'] ?? null,
            'rating'     => (int) $data['rating'],
            'comment'    => $data['comment'],
            // کاربرهای واردشده منتشر می‌شوند؛ مهمان‌ها پس از تأیید مدیر
            'is_approved' => $request->user() !== null,
        ]);

        return back()
            ->with(
                $request->user() !== null
                    ? ['success' => 'نظر شما ثبت و منتشر شد. سپاس از همراهی!']
                    : ['success' => 'نظر شما ثبت شد و پس از تأیید مدیر منتشر می‌گردد.']
            );
    }
}
