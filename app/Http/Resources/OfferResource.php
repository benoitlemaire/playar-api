<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'user' => new UserResource($this->whenLoaded('user')),
            'company_logo' => $this->company_logo,
            'description' => $this->description,
            'apply' =>  UserResource::collection($this->whenLoaded('apply')),
        ];
    }
}
