<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shop;

class ProductService
{

    /**
     * @param int $shopId
     * @return mixed
     */
    public function getProductsForShop(Shop $shop)
    {
        return Product::where('shop_id', $shop['id'])->get();
    }

    /**
     * @param $shopId
     * @param $name
     * @param $price
     * @param $stock
     * @param $category
     * @return Product
     */
    public function saveProduct($shopId, $name, $price, $stock, $category): Product
    {
        return Product::create([
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'shop_id' => $shopId,
            'category' => $category,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getProductById($id) : Product
    {
        return Product::find($id);
    }

    /**
     * @param $product
     * @param $name
     * @param $price
     * @param $stock
     * @param $category
     */
    public function updateProduct($product, $name, $price, $stock, $category) : void
    {
        $product->name = $name;
        $product->price = $price;
        $product->stock = $stock;
        $product->category = $category;

        $product->save();
    }

    /**
     * @param Product $product
     * @return void
     */
    public function deleteProduct(Product $product) : void{
        $product->delete();
    }
}
