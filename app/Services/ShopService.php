<?php

namespace App\Services;

use App\Models\Shop;

class ShopService
{
    /**
     * @return Shop[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllShops()
    {
        return Shop::all();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getShopById($id)
    {
        return Shop::find($id);
    }

    /**
     * @param $userId
     * @param $name
     * @param $description
     * @param $storedName
     * @return Shop
     */
    public function saveShop($userId, $name, $description, $storedName): Shop
    {
        return Shop::create([
            'name' => $name,
            'user_id' => $userId,
            'description' => $description,
            'image_url' =>
                $storedName != null
                    ? ImageService::SHOP_FILE_DIRECTORY . $storedName
                    : null
        ]);
    }

    /**
     * @param $shop
     * @param $name
     * @param $description
     * @param $image_url
     * @return Shop
     */
    public function updateShop($shop, $name, $description, $image_url = null): Shop
    {
        $shop->name = $name;
        $shop->description = $description;
        $shop->image_url = $image_url;

        $shop->save();

        return $shop;
    }

    /**
     * @param $shop
     * @return void
     */
    public function deleteShop($shop): void
    {
        $shop->delete();
    }
}
