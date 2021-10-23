<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Response;
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
        return response($shops = $shop::with('getProductsRelation')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $shop = Shop::where('user_id', $user['id'])->get()->first();

        if($shop){
            return response('Only 1 shop allowed per user', 200);
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image_url' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($request->hasFile('image_url')){
            $path = $request->file('image_url');

            $filenameWithExt = $request->file('image_url')->getClientOriginalName();

            //Get filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //$filename = basename($path, '.php');

            //Get just extension
            $extension = $request->file('image_url')->getClientOriginalExtension();

            //Filename to store
            $filenameToStore = $filename.'_'.time().'.'.$extension;

            //Upload Imagepath
            $request->file('image_url')->storeAs('public/image/shops', $filenameToStore);


        }else{
            $filenameToStore = 'noimage.jpg';
        }

        $user = auth()->user();

        return Response(Shop::create([
            'name' => $request->input('name'),
            'user_id' => $user['id'],
            'description' => $request->input('description'),
            'image_url' => '/storage/image/shops/' . $filenameToStore,
        ]), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Response(Shop::find($id), 200);
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
        if(!$this->isOwner($id)){
            return response('Unauthenticated', 403);
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image_url' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

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
        if(!$this->isOwner($id)){
            return response('Unauthenticated', 403);
        }

        Shop::destroy($id);
        return response('Shop successfully deleted', 200);
    }

    /**
     * Check if user is allowed for actions
     *
     * @param int $id
     * @return bool
     */
    public function isOwner($id){
        $user = auth()->user();
        $shop = Shop::where('id', $id)->get()->first();

        if($user['id'] !== $shop['user_id']){
            return false;
        }

        return true;
    }
}
