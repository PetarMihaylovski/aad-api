<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    const SHOP_FILE_DIRECTORY = 'public/images/shops/';
    const PRODUCT_FILE_DIRECTORY = 'public/images/products/';

    const SHOP_PUBLIC_DIRECTORY = '/storage/images/shops/';
    const PRODUCT_PUBLIC_DIRECTORY = '/storage/images/products/';

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
            Storage::putFileAs(self::SHOP_FILE_DIRECTORY, $file, $storedName);
        } else {
            Storage::putFileAs(self::PRODUCT_FILE_DIRECTORY, $file, $storedName);
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
