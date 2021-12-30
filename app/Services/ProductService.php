<?php

namespace App\Services;

use App\Models\Image;
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
        return Product::where('shop_id', $shop->id)->get();
    }

    /**
     * @param Product $product
     * @return mixed
     */
    public function getProductImages(Product $product){
        return Image::where('product_id', $product['id'])->get();
    }
}
