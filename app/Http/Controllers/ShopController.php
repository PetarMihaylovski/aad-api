<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Symfony\Component\Console\Input\Input;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Shop::all(), 200);
    }


    public function getAllProdutsFromShop($id){
        $shop = Shop::find($id);
        //return $shops = Shop::with('getProductsRelation')->get();
        return $shops = $shop::with('getProductsRelation')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge([
            'image-url' => 'test'
        ]);

        $fields = $request->validate([
            'user-id' => 'required',
            'description' => 'required',
            'image-url' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($request->hasFile('image-url')){
            $path = $request->file('image-url');


            $filenameWithExt = $request->file('image-url')->getClientOriginalName();

            //Get filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //$filename = basename($path, '.php');

            //Get just extension
            $extension = $request->file('image-url')->getClientOriginalExtension();

            //Filename to store
            $filenameToStore = $filename.'_'.time().'.'.$extension;

            //Upload Imagepath
            $request->file('image-url')->storeAs('public/image', $filenameToStore);


        }else{
            $filenameToStore = 'noimage.jpg';
        }

        return Shop::create([
            'user-id' => $request->input('user-id'),
            'description' => $request->input('description'),
            'image-url' => 'public/image/'.$filenameToStore,



        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Shop::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::find($id);

        $shop->update($request->all());

        return response('Updated', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Shop::destroy($id);
        return response('Shop successfully deleted', 200);
    }
}
