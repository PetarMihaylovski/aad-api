<?php

namespace App\Services;

use App\Models\Shop;

class UserService
{

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
