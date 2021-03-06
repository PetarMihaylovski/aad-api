<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Services\ImageService;
use App\Services\ShopService;
use App\Services\UserService;
use App\Services\ValidatorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ShopController extends Controller
{
    private $shopService;
    private $userService;
    private $imageService;
    private $validatorService;

    public function __construct(UserService      $userService,
                                ShopService      $shopService,
                                ImageService     $imageService,
                                ValidatorService $validatorService)
    {
        $this->userService = $userService;
        $this->shopService = $shopService;
        $this->imageService = $imageService;
        $this->validatorService = $validatorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(new ShopCollection($this->shopService->getAllShops()), ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $this->userService->getAuthUser();

        // throw error in case the user already owns a shop
        if ($user->has_shop) {
            throw new CustomException("A user cannot have more than 1 web shop", ResponseAlias::HTTP_FORBIDDEN);
        }

        $this->validatorService->validate($request->all(),[
            'name' => 'required',
            'description' => 'required',
            'image_url' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $storedImageName = null;
        if ($request->hasFile('image_url')) {
            // store the image in case it exists
            $storedImageName = $this->imageService->storeImage($request->file('image_url'), true);
        }

        $shop = $this->shopService->saveShop(
            $user['id'],
            $request->input('name'),
            $request->input('description'),
            $storedImageName
        );
        $this->userService->setOwnShop(true);

        return response(new ShopResource($shop), ResponseAlias::HTTP_CREATED);
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
        if ($shop == null) {
            throw new CustomException("Shop with ID: {$id} not found!", ResponseAlias::HTTP_NOT_FOUND);
        }
        return response(new ShopResource($shop), ResponseAlias::HTTP_OK);
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
        $shop = $this->shopService->getShopById($id);
        if ($shop == null){
            throw new CustomException("Shop with Id: {$id} does not exist!", ResponseAlias::HTTP_NOT_FOUND);
        }
        else if (!$this->userService->isShopOwner($shop)) {
            throw new CustomException("Cannot edit a shop, that you do not own!", ResponseAlias::HTTP_FORBIDDEN);
        }

        // validate the request
        $this->validatorService->validate($request->all(),[
            'name' => 'required',
            'description' => 'required',
        ]);

        $this->shopService->updateShop(
            $shop,
            $request->input('name'),
            $request->input('description'),
        );

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = $this->shopService->getShopById($id);
        if (!$this->userService->isShopOwner($shop)) {
            throw new CustomException("Cannot delete a shop, that you do not own", ResponseAlias::HTTP_FORBIDDEN);
        }
        $this->shopService->deleteShop($shop);
        $this->userService->setOwnShop(false);

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
