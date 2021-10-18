<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'shop_id' => 'required',
            'category' => 'required',
        ]);

        return Response(Product::create($request->all(), 201));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(Product::find($id), 200);
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
        $authUserId = Auth::id();
        $product = Product::find($id);
        $shop = Shop::find($product['shop_id']);

        if($shop['user_id'] !== $authUserId){
            return response('Unauthenticated', 403);
        }

        $product->update($request->all());

        return response($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $authUserId = Auth::id();
        $product = Product::find($id);
        $shop = Shop::find($product['shop_id']);

        if($shop['user_id'] !== $authUserId){
            return response('Unauthenticated', 403);
        }

        Product::destroy($id);
        return response('Product successfully deleted', 200);

    }
}
