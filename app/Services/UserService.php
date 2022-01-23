<?php

namespace App\Services;

use App\Models\User;

class UserService
{

    /**
     * returns the current authenticated user,
     * based on the provided token
     *
     * @return User
     */
    public function getAuthUser(): User
    {
        return auth()->user();
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->firstOrFail();
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @param $address
     * @param $postal
     * @return User
     */
    public function createUser($username, $email, $password, $address, $postal) : User{
        return User::create([
            'username' => $username,
            'email' => $email,
            'password' => bcrypt($password),
            'address' => $address,
            'postal' => $postal,
        ]);
    }

    /**
     * updates weather a user makes or deletes a web shop
     *
     * @return void
     */
    public function setOwnShop(bool $owns)
    {
        $this->getAuthUser()->has_shop = $owns;
        $this->getAuthUser()->save();
    }

    /**
     * checks if the user is the owner
     * of the given shop
     * @param $id
     * @return bool
     */
    public function isShopOwner($shop)
    {
        $user = auth()->user();
        if ($user->has_shop) {
            return $user['id'] === $shop['user_id'];
        }
        return false;
    }
}
