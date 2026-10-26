<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Vanilo\Product\Models\ProductProxy;

/**
 * مدیریت موجودی انبار:
 *  - بررسی کفایت موجودی اقلام سبد پیش از ثبت سفارش
 *  - کسر موجودی و ثبت آمار فروش پس از ثبت سفارش
 */
class StockManager
{
    /** استخراج تعداد از آیتم سبد (سازگار با CartItem وانیلو و آبجکت‌های ساده) */
    public static function itemQuantity($item): int
    {
        if (method_exists($item, 'getQuantity')) {
            return (int) $item->getQuantity();
        }

        return (int) ($item->quantity ?? $item->qty ?? 0);
    }

    /**
     * اقلامی که موجودی‌شان کافی نیست.
     *
     * @return array<int, array{product: \Vanilo\Product\Models\Product, requested: int, available: int, name: string}>
     */
    public static function findShortages(iterable|Collection $cartItems): array
    {
        $shortages = [];

        foreach ($cartItems as $item) {
            $product = ProductProxy::find($item->product_id);

            if (!$product) {
                continue;
            }

            $stock = (int) ($product->stock ?? 0);
            $qty   = static::itemQuantity($item);

            if ($qty > $stock) {
                $shortages[] = [
                    'product'   => $product,
                    'requested' => $qty,
                    'available' => $stock,
                    'name'      => $product->name . ($stock > 0 ? " (موجودی: " . Persian::digits($stock) . ")" : ' (ناموجود)'),
                ];
            }
        }

        return $shortages;
    }

    /**
     * کسر موجودی به ازای اقلام سفارش و به‌روزرسانی آمار فروش.
     */
    public static function deductForCartItems(iterable|Collection $cartItems): void
    {
        foreach ($cartItems as $item) {
            $product = ProductProxy::find($item->product_id);

            if (!$product) {
                continue;
            }

            $qty = static::itemQuantity($item);

            // کسر موجودی (کف: صفر) + شمارش فروش
            $remaining = max(0, (int) ($product->stock ?? 0) - $qty);
            $product->update(['stock' => $remaining]);
        }
    }
}
