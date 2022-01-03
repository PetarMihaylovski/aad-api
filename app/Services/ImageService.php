<?php

namespace App\Services;

use App\Models\Image;

class ImageService
{
    const SHOP_FILE_DIRECTORY = 'public/image/shops/';
    const PRODUCT_FILE_DIRECTORY = 'public/image/products/';

    const SHOP_PUBLIC_DIRECTORY = '/storage/image/shops/';
    const PRODUCT_PUBLIC_DIRECTORY = '/storage/image/products/';

    /**
     * @param $file ... the received image file
     * @return string the name of the stored image
     */
    public function storeImage($file, $isShop): string
    {
        $fullName = $file->getClientOriginalName();
        $fileName = pathinfo($fullName, PATHINFO_FILENAME);
        //Get just extension
        $extension = $file->getClientOriginalExtension();
        //Filename to store
        $storedName = $fileName . '_' . time() . '.' . $extension;
        if ($isShop) {
            $file->storeAs(self::SHOP_FILE_DIRECTORY, $storedName);
        } else {
            $file->storeAs(self::PRODUCT_FILE_DIRECTORY, $storedName);
        }
        return $storedName;
    }

    /**
     * @param $productId
     * @param $fileName
     * @return Image
     */
    public function saveImage($productId, $fileName): Image
    {
        return Image::create([
            'product_id' => $productId,
            'name' => $fileName,
            'path' => self::PRODUCT_PUBLIC_DIRECTORY . $fileName
        ]);
    }
}
