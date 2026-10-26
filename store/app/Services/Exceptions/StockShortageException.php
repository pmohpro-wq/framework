<?php

namespace App\Services\Exceptions;

use RuntimeException;

/**
 * وقتی پرتاب می‌شود که موجودی انبار برای اقلام سبد کافی نباشد.
 * فهرست نام‌ها آمادهٔ نمایش به مشتری است.
 */
class StockShortageException extends RuntimeException
{
    /** @param list<string> $productNames */
    public function __construct(public readonly array $productNames)
    {
        parent::__construct('موجودی انبار کافی نیست: ' . implode('، ', $productNames));
    }
}
