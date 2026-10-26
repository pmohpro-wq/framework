<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Konekt\User\Models\UserProxy;
use Vanilo\Foundation\Models\Customer;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:60'],
            'lastname'  => ['required', 'string', 'max:60'],
            'email'     => ['required', 'email', 'max:120', 'unique:users,email'],
            'phone'     => ['required', 'string', 'max:20'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique'  => 'این ایمیل قبلاً ثبت شده است. وارد شوید یا ایمیل دیگری بزنید.',
            'password.min'  => 'گذرواژه باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed' => 'تکرار گذرواژه مطابقت ندارد.',
        ]);

        /** @var \Konekt\User\Models\User $user */
        $user = UserProxy::create([
            'name'      => trim($data['firstname'] . ' ' . $data['lastname']),
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'type'      => 'client',
            'is_active' => true,
        ]);

        // ساخت پروفایل مشتری وانیلو و اتصال آن به کاربر
        $customer = Customer::create([
            'type'      => 'individual',
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'phone'     => $data['phone'],
        ]);

        $user->update(['customer_id' => $customer->id]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('account.dashboard')
            ->with('success', 'خوش آمدید! حساب کاربری شما ساخته شد.');
    }
}
