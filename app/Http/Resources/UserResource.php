<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'address' => $this->address,
            'postal' => $this->postal,
            'isShopOwner' => $this->has_shop == 1, // so it evaluates to boolean, not as number
            'created' => $this->created_at
        ];
    }
}
