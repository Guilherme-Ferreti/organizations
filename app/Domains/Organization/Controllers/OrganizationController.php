<?php

namespace App\Domains\Organization\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Rules\ExistingInterestName;
use App\Domains\Organization\Resources\OrganizationResource;

class OrganizationController extends Controller
{
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'fantasy_name'          => ['required', 'string', 'max:255'],
            'corporate_name'        => ['required', 'string', 'max:255', 'unique:organizations'],
            'domain'                => ['nullable', 'string', 'max:255', 'alpha_num', 'unique:organizations'],
            'cpf_cnpj'              => ['required', 'string', 'max:14', 'unique:organizations'],
            'logo'                  => ['nullable', 'image'],
            'social_contract'       => ['nullable', 'image'],
            'organization_type_id'  => ['required', 'integer', 'exists:organization_types,id'],
            'interests'             => ['nullable', 'array'],
            'interests.*'           => ['string', new ExistingInterestName]
        ]);

        if ($request->file('logo')?->isValid()) {
            $attributes['logo'] = $request->file('logo')->store('organizations/logos', 'public');
        }
        
        if ($request->file('social_contract')?->isValid()) {
            $attributes['social_contract'] = $request->file('social_contract')->store('organizations/social_contracts', 'public');
        }

        $organization = Organization::create($attributes);
        
        $organization->addMember($request->user(), is_owner: true);

        return $this->respondCreated(new OrganizationResource($organization));
    }

    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        
        $attributes = $request->validate([
            'fantasy_name'          => ['string', 'max:255'],
            'corporate_name'        => ['string', 'max:255', Rule::unique('organizations')->ignore($organization)],
            'domain'                => ['nullable', 'string', 'max:255', 'alpha_num', Rule::unique('organizations')->ignore($organization)],
            'cpf_cnpj'              => ['string', 'max:14', Rule::unique('organizations')->ignore($organization)],
            'logo'                  => ['nullable', 'image'],
            'social_contract'       => ['nullable', 'image'],
            'organization_type_id'  => ['integer', 'exists:organization_types,id'],
            'interests'             => ['nullable', 'array'],
            'interests.*'           => ['string', new ExistingInterestName],
        ]);

        if ($request->file('logo')?->isValid()) {
            $attributes['logo'] = $request->file('logo')->store('organizations/logos', 'public');
        }
        
        if ($request->file('social_contract')?->isValid()) {
            $attributes['social_contract'] = $request->file('social_contract')->store('organizations/social_contracts', 'public');
        }

        $organization->fill($attributes);

        if ($organization->isDirty('logo')) {
            Storage::disk('public')->delete($organization->getOriginal('logo'));
        }

        if ($organization->isDirty('social_contract')) {
            Storage::disk('public')->delete($organization->getOriginal('social_contract'));
        }
        
        $organization->save();

        return (new OrganizationResource($organization));
    }
}
