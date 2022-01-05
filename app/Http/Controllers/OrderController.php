<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\ShopService;
use App\Services\UserService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderController extends Controller
{

    private $userService;
    private $shopService;
    private $productService;
    private $orderService;
    private $validatorService;

    public function __construct(UserService      $userService,
                                ShopService      $shopService,
                                ProductService   $productService,
                                OrderService     $orderService,
                                ValidatorService $validatorService)
    {
        $this->userService = $userService;
        $this->shopService = $shopService;
        $this->productService = $productService;
        $this->orderService = $orderService;
        $this->validatorService = $validatorService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validatorService->validate($request->all(), [
            'shop_id' => 'required|integer',
            'products.*.product_id' => 'required|integer',
            'products.*.quantity' => 'required|integer'
        ]);

        // the user who made the order, this is not the shop owner!
        $user = $this->userService->getAuthUser();
        $shop = $this->shopService->getShopById($request['shop_id']);
        if ($shop == null) {
            throw new CustomException("Shop with ID: {$request['shop_id']} does not exist!",
                ResponseAlias::HTTP_NOT_FOUND);
        }

        // using db transaction to create the order and order products
        // in case a product with the given id is not found,
        // the transaction needs to be rolled back

        // doing it manually so the code does not get very nested
        DB::beginTransaction();
        $order = $this->orderService->createOrder(
            $user['id'],
            $shop['id']
        );

        foreach ($request['products'] as $orderDetails) {
            $product = $this->productService->getProductById($shop, $orderDetails['product_id']);
            if ($product == null) {
                // rollback the transaction, because order is invalid
                DB::rollBack();
                throw new CustomException("Product with ID: {$orderDetails['product_id']} does not exist",
                    ResponseAlias::HTTP_NOT_FOUND);
            }

            $inStock = $this->productService->isInStock($product, $orderDetails['quantity']);
            if (!$inStock) {
                DB::rollBack();
                throw new CustomException("Not enough products in stock to fulfill your order",
                    ResponseAlias::HTTP_NOT_FOUND);
            }

            $this->orderService->createOrderProduct(
                $order['id'],
                $product,
                $orderDetails['quantity']
            );
        }
        // end of transaction, everything was correct
        DB::commit();
        return response(new OrderResource($order->loadMissing(['items'])), ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
}
