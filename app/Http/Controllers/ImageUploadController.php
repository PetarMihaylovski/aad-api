<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function productUploadImage(Request $request){
        $request->validate([
            'image-url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product-id.*' => 'required'
        ]);

        if($request->has('image-url')){
            foreach($request->file('filename') as $image)
            {
                $name=$image->getClientOriginalName();
                $image->move(public_path().'public/images/', $name);
                $data[] = $name;
            }
        }

    }


}
