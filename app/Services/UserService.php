<?php

namespace App\Services;

use App\Models\User;

class UserService
{

    /**
     * @return User
     */
    public function getUser() : User {
        return auth()->user();
    }

    /**
     * @return void
     */
    public function removeOwnedShop() {
        $this->getUser()->has_shop = false;
        $this->getUser()->save();
    }

    /**
     * @param $id
     * @return bool
     */
    public function isShopOwner($shop){
        $user = auth()->user();
        if ($user->has_shop){
            return $user['id'] === $shop['user_id'];
        }
        return false;
    }
}
