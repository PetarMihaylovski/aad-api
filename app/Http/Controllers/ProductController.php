<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Image;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Return all products with images
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        $productsWithImages = [];
        foreach ($products as $product){
            $images = Image::where('product_id', $product['id'])->get();
            array_push($productsWithImages, $product, $images);
        }

        return response($productsWithImages, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request){
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
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if(!$product){
            return Response('Product does not exist', 404);
        }

        $productsWithImages = [];
        $images = Image::where('product_id', $product['id'])->get();

        array_push($productsWithImages, $product, $images);

        return response($productsWithImages, 200);
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
        if(!$this->isOwner($id)){
            return response('Unauthenticated', 403);
        }

        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'category' => 'required',
        ]);

        $product = Product::find($id);

        $product->update($request->all());

        return response($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!$this->isOwner($id)){
            return response('Unauthenticated', 403);
        }

        return response(Product::destroy($id), 200);
    }

    /**
     * Get resources with specific category
     *
     * @param int $query
     * @return \Illuminate\Http\Response
     */
    public function filter($query){
        $products = Product::where('category', $query)->get();

        return response($products, 200);
    }

    /**
     * Check if user is allowed for actions
     *
     * @param int $id
     * @return bool
     */
    public function isOwner($id){
        $user = auth()->user();
        $shop = Shop::where('user_id', $user['id'])->get()->first();
        $product = Product::find($id);

        if($product['shop_id'] !== $shop['id']){
            return false;
        }

        return true;
    }
}
