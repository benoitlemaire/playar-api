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
            'name' => $this->name,
            'email' => $this->email,
            'document_freelance' => $this->document_freelance,
            'instagram_account' => $this->instagram_account,
            'filter_video' => $this->filter_video,
            'phone' => $this->phone,
            'validated' => $this->validated,
            'roles' => RoleResource::collection($this->roles)
        ];
    }
}
