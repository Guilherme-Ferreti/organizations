<?php

namespace App\Domains\Organization\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
   public static $wrap = 'organization';

    public function toArray($request)
    {
        return [
            'uuid'              => $this->uuid,
            'fantasy_name'      => $this->fantasy_name,
            'corporate_name'    => $this->corporate_name,
            'domain'            => $this->domain,
            'cpf_cnpj'          => $this->cpf_cnpj,
            'logo'              => $this->logo ? asset('storage/' . $this->logo): null,
            'social_contract'   => $this->social_contract ? asset('storage/' . $this->social_contract) : null,
            'organization_type' => $this->organization_type,
            'interests'         => $this->interests,
            'registered_date'   => $this->created_at,
            'members'           => MemberResource::collection($this->whenLoaded('members')),
        ];
    }
}
