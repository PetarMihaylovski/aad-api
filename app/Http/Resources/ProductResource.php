<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'shopId' => $this->shop_id,
            'name' => $this->name,
            'price' => $this->price,
            'category' => $this->category,
            'inStock' => $this->stock > 0,
            'stock' => $this->stock,
            'updated'=>$this->updated_at,
            'images' => ImageResource::collection($this->whenLoaded('images'))
        ];
    }
}
