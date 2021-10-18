<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageUploadController extends Controller
{
    public function uploadProductImages(Request $request){

        $request->validate([
            'images' => 'required',
        ]);

        if($request->hasFile('images')){
            $images = $request->file('images');

            foreach ($images as $image){
                $filenameWithExt = $image->getClientOriginalName();
                //Get filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                //Get just extension
                $extension = $image->getClientOriginalExtension();

                //Filename to store
                $filenameToStore = $filename.'_'.time().'.'.$extension;

                //Upload Imagepath
                $image->storeAs('public/image', $filenameToStore);

                Image::create([
                    'product_id' => '1',
                    'path' => 'public/image/'.$filenameToStore
                ]);
            }
        }

        return response('Images uploaded', 201);
    }


}
