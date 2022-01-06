<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProduct;

class OrderService
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getOrdersByUserId($userId){
        return Order::where('user_id', $userId)->get();
    }

    public function getOrderByOrderId($userId, $orderId){
        return Order::where([
            ['user_id', $userId],
            ['id', $orderId]
        ])->first();
    }

    public function createOrder($userId, $shopId){
        return Order::create([
            'user_id' => $userId,
            'shop_id' => $shopId
        ]);
    }

    public function createOrderProduct($orderId, $product, $orderQuantity){
        $total = $product->price * $orderQuantity;
        $orderProduct = OrderProduct::create([
            'order_id' => $orderId,
            'product_id' => $product->id,
            'quantity' => $orderQuantity,
            'total' => $total
        ]);
        $this->productService->decrementStock($product, $orderQuantity);
        return $orderProduct;
    }

}
