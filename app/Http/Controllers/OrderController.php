<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Ramsey\Uuid\Uuid;
use App\Models\Histoy;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $userStore = Auth::user()->store;

        if (!$userStore) {
            return redirect()->route('addstore');
        }

        $storeId = $userStore->id;

        $orders = Order::where('store_id', $storeId)
            ->with(['cart.user', 'cart.chair', 'cart.cartMenus.menu'])
            ->get();

        $statuses = [];

        foreach ($orders as $order) {
            try {
                if ($order->status === 'settlement' && $order->payment_type === 'cash') {
                    $statuses[$order->no_order] = (object) [
                        'status' => $order->status,
                        'bg_color' => 'text-white text-center bg-green-500 w-fit rounded-xl'
                    ];
                    continue; // Skip further processing for this order
                }

                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = true;

                $status = \Midtrans\Transaction::status($order->no_order);

                $order->update([
                    'status' => $status->transaction_status,
                    'payment_type' => $status->payment_type ?? null,
                ]);

                if ($status->transaction_status === 'expire') {
                    $order->delete();
                    continue;
                }

                $statuses[$order->no_order] = (object) [
                    'status' => $status->transaction_status,
                    'bg_color' => $status->transaction_status === 'settlement' ? 'text-white text-center bg-green-500 w-fit rounded-xl' : 'text-white text-center bg-red-500 w-fit rounded-xl'
                ];
            } catch (\Exception $e) {
                $statuses[$order->no_order] = (object) [
                    'status' => 'Error: ' . $e->getMessage(),
                    'bg_color' => 'bg-red-500 w-fit text-white text-center rounded-xl'
                ];
            }
        }

        return view('order', compact('orders', 'statuses'));
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();

        $storeId = $user->store->id;

        if (!$storeId) {
            return redirect('/')->withErrors('No store associated with the user.');
        }

        $cart = $user->carts()->latest()->first();

        if (!$cart) {
            $cart = $user->carts()->create([
                'store_id' => $storeId,
            ]);
        }

        return view('addorder', compact('cart'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();
        $storeId = $user->store->id;

        if (!$storeId) {
            return redirect('/')->withErrors('No store associated with the user.');
        }

        $request->validate([
            'no_telpon' => 'required|string|max:15',
            'atas_nama' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $cart = $user->carts()->with('user', 'cartMenus.menu')
                ->latest()->first(); // Lock the row for this cart
            if (!$cart) {
                DB::rollBack(); // Rollback if no cart is found
                return redirect('/')->withErrors('No cart found.');
            }

            $cart->lockForUpdate();

            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = true;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $orderId = 'ORDER-' . strtoupper(substr(Uuid::uuid4()->toString(), 0, 8));

            $items = $cart->cartMenus->map(function ($cartMenu) {
                return [
                    'id' => $cartMenu->menu_id,
                    'price' => (int) $cartMenu->menu->price,
                    'quantity' => (int) $cartMenu->quantity,
                    'name' => $cartMenu->menu->name,
                ];
            })->toArray();

            $billing_address = [
                'first_name' => $request->atas_nama,
                'last_name' => '',
                'address' => $request->alamat ?? 'N/A',
                'city' => 'N/A',
                'postal_code' => 'N/A',
                'phone' => $request->no_telpon,
                'country_code' => 'IDN',
            ];

            $shipping_address = $billing_address;

            $customer_details = [
                'first_name' => $request->atas_nama,
                'last_name' => '',
                'email' => $user->email,
                'phone' => $request->no_telpon,
                'billing_address' => $billing_address,
                'shipping_address' => $shipping_address,
            ];

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $cart->total_amount + ($request->ongkir ?? 0),
                ],
                'item_details' => $items,
                'customer_details' => $customer_details,
            ];

            // Create order entry
            $order = new Order();
            $order->store_id = $storeId;
            $order->cart_id = $cart->id;
            $order->no_order = $orderId;
            $order->atas_nama = $request->atas_nama;
            $order->no_telpon = $request->no_telpon;
            $order->save();

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $user->carts()->create();

            DB::commit();

            return view('checkout', compact('snapToken', 'order'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/')->withErrors('An error occurred. Please try again.');
        }
    }

    public function archive($orderId)
    {
        $order = Order::find($orderId);

        $user = auth()->user();

        $settlement = $user->settlements()->latest()->first();

        if (!$settlement) {
            $settlement = new Settlement();
            $settlement->user_id = $user->id;
            $settlement->store_id = $user->store->id;
            $settlement->start_time = now(); // Set as needed
            $settlement->start_amount = 0; // Initialize as needed
            $settlement->total_amount = 0; // Initialize as needed
            $settlement->expected = 0; // Initialize as needed
            $settlement->save();
        }

        DB::transaction(function () use ($order, $settlement, $user) {
            $history = new Histoy();
            $history->id = $order->id;
            $history->store_id = $user->store->id;
            $history->no_order = $order->no_order;
            $history->kursi = $order->cart->user->name;
            $history->name = $order->atas_nama;
            $orderDetails = '';

            foreach ($order->cart->cartMenus as $cartMenu) {
                $orderDetails .= $cartMenu->menu->name . ' - ' . $cartMenu->quantity . ' - ' . $cartMenu->notes . ' - ';
            }

            $history->order = $orderDetails;
            $history->total_amount = $order->cart->total_amount;
            $history->status = $order->status;
            $history->payment_type = $order->payment_type;
            $history->settlement_id = $settlement->id; // Set the settlement_id

            $history->save();

            $totalHistoyAmount = $settlement->histoys()->sum('total_amount');
            $settlement->expected = $totalHistoyAmount + $settlement->start_amount;
            $settlement->save();

            foreach ($order->cart->cartMenus as $cartMenu) {
                $cartMenu->delete();
            }

            $order->cart->delete();

            $order->delete();

            Cache::forget('settlements_user_' . Auth::id());
        });

        return redirect()->back()->with('success', 'Order archived successfully');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::where('id', $id)->lockForUpdate()->first();

            if (!$order) {
                DB::rollBack(); // Rollback if the order is not found
                return redirect(route('order'))->withErrors('Order not found.');
            }

            $order->delete();

            DB::commit();

            return redirect(route('order'))->with('success', 'Order Berhasil Dihapus !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect(route('order'))->withErrors('An error occurred. Please try again.');
        }
    }

    public function cashpayment(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::find($orderId);

        if ($order) {
            $order->status = 'settlement';
            $order->payment_type = 'cash';
            $order->save();

            $newCart = new Cart();
            $newCart->user_id = $order->cart->user_id;
            $newCart->save();

            return redirect()->route('order')->with('success', 'Cash payment successful!');
        }

        return redirect()->route('order')->with('error', 'Cash payment failed!');
    }
}
