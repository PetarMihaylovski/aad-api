<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'shopId' => $this->id,
            'ownerId' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'imageName' => $this->image_name,
            'imageURL' => $this->image_url,
        ];
    }
}
