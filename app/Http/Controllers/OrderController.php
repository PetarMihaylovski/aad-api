<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user['id'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $orders = $request->validate([
            '*.product_id' => 'required',
            '*.stock' => 'required',
        ]);

        $user = auth()->user();

        foreach ($orders as $order){


            $shopOwner = Shop::where('user_id', $user['id'])->first();
            $shopMail = User::where('id', $shopOwner['user_id'])->first();

            $product = Product::find($order['product_id']);

            Order::create([
                'user_id' => $user['id'],
                'shop_id' => $shopOwner['id'],
                'product_id' => $order['product_id'],
                'stock' => $order['stock'],
                'price' => $product['price'],
            ]);

            $stock = $product['stock'] - $order['stock'];

            if($stock < 0){
                return response($product->name.' sold out!', 200);
            }

            $product->update([
                'stock' => $stock
            ]);

            $totalPrice = $order['stock'] * $product['price'];

            $details = [
                'title' => 'Order placed',
                'product' => $product,
                'user' => $user,
                'total' => number_format($totalPrice, 2),
                'stock' => $order['stock']
            ];

            Mail::to($shopMail['email'])->send(new OrderMail($details));
        }
        return response('Order placed', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
