<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Vanilo\Product\Models\ProductProxy;

class HomeController extends Controller
{
    public function index()
    {
        $products = ProductProxy::actives()
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        return view('store.home', ['products' => $products]);
    }
}
