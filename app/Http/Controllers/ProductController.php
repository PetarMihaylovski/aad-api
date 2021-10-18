<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Image;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Product::all(), 200);
    }

    public function storeOneProduct(Request $request){
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'category' => 'required',
            'images[]' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);


        $user = auth()->user();
        $shop = Shop::where('user_id', $user['id'])->get()->first();


       $product =  Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'shop_id' => $shop['id'],
            'category' => $request->input('category'),
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
                    'product_id' => $product['id'],
                    'path' => 'public/image/'.$filenameToStore
                ]);
            }
        }
        return response($product, 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            '*.name' => 'required',
            '*.price' => 'required',
            '*.stock' => 'required',
            '*.shop_id' => 'required',
            '*.category' => 'required',
            '*.images' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $rsp = [];
        foreach ($request->all() as $data) {
            $container = new Product([
                'name' => $data['name'],
                'price' => (double)$data['price'],
                'stock' => (int)$data['stock'],
                'shop_id' => $data['shop_id'],
                'category' => $data['category']
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

            $container->save();
            array_push($rsp, $container);
        }
        return response($rsp, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(Product::where('shop_id', $id)->get(), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
