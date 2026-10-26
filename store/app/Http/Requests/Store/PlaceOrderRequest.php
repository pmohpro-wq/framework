<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

/**
 * اعتبارسنجی فرم تسویه‌حساب (ثبت سفارش).
 *
 * پیام‌ها فارسی‌اند چون این فرم مستقیماً رو به مشتری است.
 */
class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname'       => ['required', 'string', 'max:60'],
            'lastname'        => ['required', 'string', 'max:60'],
            'email'           => ['required', 'email', 'max:120'],
            'phone'           => ['required', 'string', 'max:20'],
            'city'            => ['required', 'string', 'max:60'],
            'address'         => ['required', 'string', 'max:255'],
            'postalcode'      => ['nullable', 'string', 'max:12'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            'coupon'          => ['nullable', 'string', 'max:40'],
            'shipping_method' => ['required', 'integer', 'exists:shipping_methods,id'],
            'payment_method'  => ['required', 'integer', 'exists:payment_methods,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute الزامی است.',
            'email'    => 'ایمیل معتبر نیست.',
            'max'      => ':attribute نباید بیشتر از :max کاراکتر باشد.',
            'exists'   => 'گزینهٔ انتخاب‌شده معتبر نیست.',
        ];
    }

    public function attributes(): array
    {
        return [
            'firstname'       => 'نام',
            'lastname'        => 'نام خانوادگی',
            'email'           => 'ایمیل',
            'phone'           => 'شمارهٔ تماس',
            'city'            => 'شهر',
            'address'         => 'نشانی',
            'postalcode'      => 'کد پستی',
            'notes'           => 'توضیحات',
            'coupon'          => 'کد تخفیف',
            'shipping_method' => 'روش ارسال',
            'payment_method'  => 'روش پرداخت',
        ];
    }
}
