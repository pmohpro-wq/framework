<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Vanilo\Order\Models\OrderProxy;

class AccountController extends Controller
{
    /** داشبورد حساب: سفارش‌های کاربر */
    public function dashboard(Request $request)
    {
        $orders = OrderProxy::where('user_id', $request->user()->id)
            ->with('items')
            ->orderByDesc('ordered_at')
            ->orderByDesc('id')
            ->get();

        return view('store.account', [
            'user'   => $request->user(),
            'orders' => $orders,
        ]);
    }

    /** جزئیات یک سفارش از سفارش‌های خود کاربر */
    public function orderDetail(Request $request, string $number)
    {
        $order = OrderProxy::where('number', $number)
            ->where('user_id', $request->user()->id)
            ->with(['items', 'billpayer', 'shippingAddress'])
            ->firstOrFail();

        return view('store.account_order', ['order' => $order]);
    }

    /** صفحهٔ پروفایل */
    public function profile(Request $request)
    {
        return view('store.account_profile', [
            'user'     => $request->user(),
            'customer' => $request->user()->customer,
        ]);
    }

    /** به‌روزرسانی پروفایل */
    public function updateProfile(Request $request)
    {
        $user     = $request->user();
        $customer = $user->customer;

        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:60'],
            'lastname'  => ['required', 'string', 'max:60'],
            'phone'     => ['required', 'string', 'max:20'],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'گذرواژه باید حداقل ۸ کاراکتر باشد.',
        ]);

        if ($customer) {
            $customer->update([
                'firstname' => $data['firstname'],
                'lastname'  => $data['lastname'],
                'phone'     => $data['phone'],
            ]);
        }

        $update = ['name' => trim($data['firstname'] . ' ' . $data['lastname'])];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return back()->with('success', 'اطلاعات حساب با موفقیت به‌روزرسانی شد.');
    }
}
