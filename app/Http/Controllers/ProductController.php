<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Image;
use App\Services\ProductService;
use App\Services\ShopService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends Controller
{
    private $userService;
    private $shopService;
    private $productService;

    public function __construct(UserService $userService,
                                ShopService $shopService,
                                ProductService $productService)
    {
        $this->userService = $userService;
        $this->shopService = $shopService;
        $this->productService = $productService;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = $this->shopService->getShopById($id);
        if ($shop == null){
            throw new CustomException("Shop with ID: {$id} does not exist!", ResponseAlias::HTTP_NOT_FOUND);
        }

        $products = $this->productService->getProductsForShop($shop);
        if ($products == null || $products->count() == 0) {
            throw new CustomException("The shop with ID: {$id} does not have any products!", ResponseAlias::HTTP_NOT_FOUND);
        }

        $productsRsp = array();
        // creates a product response, when fetching the images of the product
        foreach ($products as $p){
            // push the created resource to the response array
            $productsRsp[] = new ProductResource($p->loadMissing(['images']));
        }
        return response($productsRsp, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'category' => 'required',
            'imageOne' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imageTwo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imageThree' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);


        $user = auth()->user();
        $shop = Shop::where('user_id', $user['id'])->get()->first();


        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'shop_id' => $shop['id'],
            'category' => $request->input('category'),
        ]);

        if ($request->hasFile('imageOne')) {
            $images = [];
            array_push($images, $request->file('imageOne'));
            array_push($images, $request->file('imageTwo'));
            array_push($images, $request->file('imageThree'));

            $imgs = [];

            // all three images are saved withing the same timestamp
            // so this counter gives them a unique part
            foreach ($images as $image) {
                $filenameWithExt = $image->getClientOriginalName();
                //Get filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                //Get just extension
                $extension = $image->getClientOriginalExtension();

                //Filename to store
                $filenameToStore = Str::uuid() . '_' . time() . '.' . $extension;

                //Upload Imagepath
                $image->storeAs('public/image/products/', $filenameToStore);

                 array_push($imgs, Image::create([
                    'product_id' => $product['id'],
                    'path' => '/storage/image/products/' . $filenameToStore
                ]));
            }
            $product->images = $imgs;
        }
        return response($product, 201);
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
        if (!$this->isOwner($id)) {
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
        if (!$this->isOwner($id)) {
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
    public function filter($query)
    {
        $products = Product::where('category', $query)->get();

        return response($products, 200);
    }

    /**
     * Check if user is allowed for actions
     *
     * @param int $id
     * @return bool
     */
    public function isOwner($id)
    {
        $user = auth()->user();
        $shop = Shop::where('user_id', $user['id'])->get()->first();
        $product = Product::find($id);

        if ($product['shop_id'] !== $shop['id']) {
            return false;
        }

        return true;
    }
}
