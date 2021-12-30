<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Services\ShopService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\Shop;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ShopController extends Controller
{
    private $shopService;
    private $userService;

    public function __construct(ShopService $shopService, UserService $userService)
    {
        $this->shopService = $shopService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(new ShopCollection(Shop::all()), ResponseAlias::HTTP_OK);
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
            'description' => 'required',
            'image_url' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = auth()->user();

        // throw error in case the user already owns a shop
        if($user->has_shop){
            throw new CustomException("A user cannot have more than 1 web shop",  ResponseAlias::HTTP_FORBIDDEN);
        }

       $storedImageName = null;
        if($request->hasFile('image_url')){
             $storedImageName = $this->shopService->storeImage($request->file('image_url'));
        }

        $shop = $this->shopService->saveShop(
            $user['id'],
            $request->input('name'),
            $request->input('description'),
            $storedImageName
        );

        $user->has_shop=true;
        $user->save();

        return response(new ShopResource($shop), ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = $this->shopService->getShopById($id);
        if ($shop == null){
            throw new CustomException("Shop with ID: {$id} not found!", ResponseAlias::HTTP_NOT_FOUND);
        }
        return response(new ShopResource($shop), ResponseAlias::HTTP_OK);
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
        $shop = $this->shopService->getShopById($id);
        if (!$this->userService->isShopOwner($shop)){
            throw new CustomException("Cannot edit a shop, that you do not own", ResponseAlias::HTTP_FORBIDDEN);
        }

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $updated = $this->shopService->updateShop(
            $shop,
            $request->input('name'),
            $request->input('description'),
            $request->input('image_url'),
        );
        return response(new ShopResource($updated), ResponseAlias::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = $this->shopService->getShopById($id);
        if (!$this->userService->isShopOwner($shop)){
            throw new CustomException("Cannot delete a shop, that you do not own", ResponseAlias::HTTP_FORBIDDEN);
        }
        $this->shopService->deleteShop($shop);
        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
