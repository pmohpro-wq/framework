<?php

namespace App\Services\Exceptions;

use RuntimeException;

/** وقتی پرتاب می‌شود که کد تخفیف نامعتبر یا منقضی باشد. */
class InvalidCouponException extends RuntimeException
{
    public function __construct(public readonly string $couponCode)
    {
        parent::__construct("کد تخفیف «{$couponCode}» معتبر نیست یا منقضی شده است.");
    }
}
