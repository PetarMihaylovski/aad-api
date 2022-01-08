<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\ProductResource;
use App\Services\ImageService;
use App\Services\ProductService;
use App\Services\ShopService;
use App\Services\UserService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends Controller
{
    private $userService;
    private $shopService;
    private $productService;
    private $imageService;
    private $validatorService;

    public function __construct(UserService      $userService,
                                ShopService      $shopService,
                                ProductService   $productService,
                                ImageService     $imageService,
                                ValidatorService $validatorService)
    {
        $this->userService = $userService;
        $this->shopService = $shopService;
        $this->productService = $productService;
        $this->imageService = $imageService;
        $this->validatorService = $validatorService;
    }

    /**
     * Display the specified resource.
     *
     * @param int $shopId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $shopId)
    {
        $shop = $this->shopService->getShopById($shopId);
        if ($shop == null) {
            throw new CustomException("Shop with ID: {$shopId} does not exist!", ResponseAlias::HTTP_NOT_FOUND);
        }

        $category = null;
        if ($request->has('category')) {
            $category = $request->query('category');
            $products = $this->productService->getAllProducts($shop, $category);
        } else {
            $products = $this->productService->getAllProducts($shop);
        }

        if ($products == null || $products->count() == 0) {
            if ($category) {
                throw new CustomException("The shop with ID: {$shopId} does not have any products matching the {$category} category!!", ResponseAlias::HTTP_NOT_FOUND);
            }
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
        $this->validatorService->validate($request->all(),[
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
    public function update(Request $request, $shopId, $productId)
    {
        $shop = $this->shopService->getShopById($shopId);
        if (!$shop) {
            throw new CustomException("Shop with ID: {$shopId} not found!",
                ResponseAlias::HTTP_NOT_FOUND);
        }
        if (!$this->userService->isShopOwner($shop)) {
            throw new CustomException("You cannot edit a product from a shop, that does not belong to you!",
                ResponseAlias::HTTP_FORBIDDEN);
        }
        $product = $this->productService->getProductById($shop, $productId);
        if (!$product) {
            throw new CustomException("Product with ID {$productId} does not exist!",
                ResponseAlias::HTTP_NOT_FOUND);
        }

        $this->validatorService->validate($request->all(),[
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'category' => 'required',
        ]);

        $this->productService->updateProduct(
            $product,
            $request->input('name'),
            $request->input('price'),
            $request->input('stock'),
            $request->input('category'),
        );

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shopId, $productId)
    {
        $shop = $this->shopService->getShopById($shopId);
        if (!$this->userService->isShopOwner($shop)) {
            throw new CustomException("You cannot delete a product from a shop, that does not belong to you!",
                ResponseAlias::HTTP_FORBIDDEN);
        }
        $product = $this->productService->getProductById($shop, $productId);
        if (!$product) {
            throw new CustomException("Product with ID {$productId} does not exist!",
                ResponseAlias::HTTP_NOT_FOUND);
        }
        $this->productService->deleteProduct($product);
        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
