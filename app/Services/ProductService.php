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
    public function getAllProducts(Shop $shop, string $category = null)
    {
        if ($category) {
            // returns only the products from the required category
            return $shop->products()->where('category', $category)->get();
        } else {
            // no explicit category wanted, so return all products
            return $shop->products();
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getProductById(Shop $shop, int $id)
    {
        return $shop->products()->where('id', $id)->get();
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
     * @param $product
     * @param $name
     * @param $price
     * @param $stock
     * @param $category
     */
    public function updateProduct($product, $name, $price, $stock, $category): void
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
    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }
}
