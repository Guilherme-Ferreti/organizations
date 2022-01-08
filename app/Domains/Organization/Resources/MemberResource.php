<?php

namespace App\Domains\Organization\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'name'                  => $this->name,
            'is_owner'              => $this->pivot->is_owner,
            'is_techinical_manager' => $this->pivot->is_techinical_manager,
            'is_active'             => $this->pivot->is_active,
        ];
    }
}
