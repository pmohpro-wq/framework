<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vanilo\Order\Models\OrderProxy;

class OrderTrackingController extends Controller
{
    public function show()
    {
        return view('store.track', [
            'order'      => null,
            'searchedFor' => null,
        ]);
    }

    public function track(Request $request)
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:30'],
        ]);

        $number = strtoupper(trim($data['order_number']));

        $order = OrderProxy::where('number', $number)
            ->with(['items', 'billpayer', 'shippingAddress'])
            ->first();

        return view('store.track', [
            'order'       => $order,
            'searchedFor' => $number,
        ]);
    }
}
