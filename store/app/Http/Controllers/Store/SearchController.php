<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vanilo\Product\Models\ProductProxy;

class SearchController extends Controller
{
    public function results(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $products = collect();

        if ($query !== '') {
            $term = str_replace(['ی', 'ك'], ['ی', 'ک'], $query);

            $products = ProductProxy::actives()
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('excerpt', 'like', "%{$term}%")
                        ->orWhere('subtitle', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                })
                ->orderByDesc('priority')
                ->orderByDesc('created_at')
                ->paginate(12)
                ->withQueryString();
        }

        return view('store.search', [
            'query'    => $query,
            'products' => $products,
        ]);
    }
}
