<?php

namespace App\Services;

class ShopService
{
    const FILE_DIRECTORY = 'public/image/shops';

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
