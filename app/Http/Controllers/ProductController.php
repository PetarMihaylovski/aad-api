<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
