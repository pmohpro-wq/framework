<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Vanilo\Product\Models\ProductProxy;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = ProductProxy::where('slug', $slug)->firstOrFail();

        $related = ProductProxy::actives()
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('store.product', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
