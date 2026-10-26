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

        // کنترل موجودی انبار
        $qty = (int) $request->input('quantity', 1);
        $inCart = (int) (Cart::getItems()->firstWhere('product_id', $product->id)?->qty ?? 0);

        if ((int) ($product->stock ?? 0) === 0) {
            return back()->with('warning', 'این کالا فعلاً ناموجود است.');
        }

        if ($inCart + $qty > (int) $product->stock) {
            return back()->with('warning', 'تعداد درخواستی بیشتر از موجودی انبار است (' . \App\Support\Persian::digits($product->stock) . ' عدد).');
        }

        Cart::create();
        Cart::addItem($product, $qty);

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
                // کنترل موجودی انبار هنگام تغییر تعداد
                $product = ProductProxy::find($item->product_id);

                if ($product && $request->integer('quantity') > (int) $product->stock) {
                    return back()->with('warning', 'تعداد درخواستی «' . $product->name . '» بیشتر از موجودی انبار است (' . \App\Support\Persian::digits($product->stock) . ' عدد).');
                }

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
