<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\ImageService;
use App\Services\ProductService;
use App\Services\ShopService;
use App\Services\UserService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    private $userService;
    private $shopService;
    private $productService;
    private $validatorService;

    public function __construct(UserService      $userService,
                                ShopService      $shopService,
                                ProductService   $productService,
                                ValidatorService $validatorService)
    {
        $this->userService = $userService;
        $this->shopService = $shopService;
        $this->productService = $productService;
        $this->validatorService = $validatorService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validatorService->validate($request->all(),[
            'shop_id' => 'required|integer',
            'products.*.product_id' => 'required|integer',
            'products.*.quantity' => 'required|integer'
        ]);

        $user = $this->userService->getAuthUser();
        foreach ($request['products'] as $order){

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
}
