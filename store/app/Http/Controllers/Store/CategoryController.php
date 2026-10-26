<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Vanilo\Category\Models\TaxonProxy;

class CategoryController extends Controller
{
    public function show(string $taxonomySlug, string $taxonSlug)
    {
        $taxon = TaxonProxy::where('slug', $taxonSlug)->firstOrFail();

        $products = $taxon->products()
            ->where('state', 'active')
            ->paginate(12);

        return view('store.category', [
            'taxon'    => $taxon,
            'products' => $products,
        ]);
    }
}
