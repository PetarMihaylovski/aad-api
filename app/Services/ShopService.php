<?php

namespace App\Services;

use App\Models\Shop;

class ShopService
{
    const FILE_DIRECTORY = 'public/image/shops';

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
                    ? self::FILE_DIRECTORY . $storedName
                    : null
        ]);
    }


    /**
     * @param $file ... the received image file
     * @return string the name of the stored image
     */
    public function storeImage($file): string
    {
        $fullName = $file->getClientOriginalName();
        $fileName = pathinfo($fullName, PATHINFO_FILENAME);
        //Get just extension
        $extension = $file->getClientOriginalExtension();
        //Filename to store
        $storedName = $fileName . '_' . time() . '.' . $extension;
        //Upload Image path
        $file->storeAs(self::FILE_DIRECTORY, $storedName);
        return $storedName;
    }
}
