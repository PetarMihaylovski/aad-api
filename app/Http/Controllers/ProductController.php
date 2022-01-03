<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\ImageCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Image;
use App\Services\ImageService;
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
    private $imageService;

    public function __construct(UserService    $userService,
                                ShopService    $shopService,
                                ProductService $productService,
                                ImageService   $imageService)
    {
        $this->userService = $userService;
        $this->shopService = $shopService;
        $this->productService = $productService;
        $this->imageService = $imageService;
    }

    /**
     * Display the specified resource.
     *
     * @param int $shopId
     * @return \Illuminate\Http\Response
     */
    public function show($shopId)
    {
        $shop = $this->shopService->getShopById($shopId);
        if ($shop == null) {
            throw new CustomException("Shop with ID: {$shopId} does not exist!", ResponseAlias::HTTP_NOT_FOUND);
        }

        $products = $this->productService->getProductsForShop($shop);
        if ($products == null || $products->count() == 0) {
            throw new CustomException("The shop with ID: {$shopId} does not have any products!", ResponseAlias::HTTP_NOT_FOUND);
        }

        $productsRsp = array();
        // creates a product response, when fetching the images of the product
        foreach ($products as $p) {
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
    public function store(Request $request, $shopId)
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

        // when here, shop exists for sure, so no need to check
        $shop = $this->shopService->getShopById($shopId);
        $userIsOwner = $this->userService->isShopOwner($shop);

        if (!$userIsOwner) {
            throw new CustomException("You cannot add products to shop that you do not own!",
                ResponseAlias::HTTP_FORBIDDEN);
        }

        $product = $this->productService->saveProduct(
            $shop['id'],
            $request->input('name'),
            $request->input('price'),
            $request->input('stock'),
            $request->input('category')
        );

        $maxImages = 3;
        // dynamically builds the image index,
        // based on the number of attached files in the request
        for ($numIndex = 1; $numIndex <= $maxImages; $numIndex++) {
            $key = 'image';
            if ($numIndex == 1) {
                $key .= 'One';
            } else if ($numIndex == 2) {
                $key .= 'Two';
            } else {
                $key .= 'Three';
            }

            if ($request->hasFile($key)) {
                $imageFile = $request->file($key);
                $fileName = $this->imageService->storeImage($imageFile, false);

                $this->imageService->saveImage($product['id'], $fileName);
            }
        }
        return response(new ProductResource($product->loadMissing(['images'])), ResponseAlias::HTTP_CREATED);
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
