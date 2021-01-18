<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'document_freelance' => $this->document_freelance,
            'instagram_account' => $this->instagram_account,
            'filter_video' => $this->filter_video,
            'phone' => $this->phone,
            'verified' => $this->verified,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'myOffers' => OfferResource::collection($this->whenLoaded('myOffers')),
            'myApplies' => OfferResource::collection($this->whenLoaded('myApplies'))
        ];
    }
}
