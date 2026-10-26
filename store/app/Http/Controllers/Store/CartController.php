<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vanilo\Cart\Facades\Cart;
use Vanilo\Product\Models\ProductProxy;

class CartController extends Controller
{
    public function index()
    {
        return view('store.cart', [
            'items' => Cart::getItems(),
            'total' => Cart::total(),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = ProductProxy::findOrFail($request->input('product_id'));

        Cart::create();
        Cart::addItem($product, (int) $request->input('quantity', 1));

        return redirect()
            ->route('cart.index')
            ->with('success', 'محصول به سبد خرید اضافه شد.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $item = Cart::getItems()->where('id', $request->input('item_id'))->first();

        if ($item) {
            if ($request->integer('quantity') === 0) {
                Cart::removeItem($item);
            } else {
                $item->quantity = $request->integer('quantity');
                $item->save();
            }
        }

        return redirect()->route('cart.index');
    }

    public function remove(Request $request)
    {
        $request->validate(['item_id' => ['required', 'integer']]);

        $item = Cart::getItems()->where('id', $request->input('item_id'))->first();

        if ($item) {
            Cart::removeItem($item);
        }

        return redirect()->route('cart.index');
    }

    public function clear()
    {
        Cart::destroy();

        return redirect()->route('cart.index');
    }
}
