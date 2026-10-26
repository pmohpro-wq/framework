<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id', 'name', 'email', 'rating', 'comment', 'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Vanilo\Product\Models\ProductProxy::modelClass());
    }

    /** میانگین امتیازهای تأییدشدهٔ یک محصول */
    public static function averageFor(int $productId): ?float
    {
        $avg = static::where('product_id', $productId)
            ->where('is_approved', true)
            ->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }
}
