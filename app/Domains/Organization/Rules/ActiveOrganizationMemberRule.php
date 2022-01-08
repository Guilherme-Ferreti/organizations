<?php

namespace App\Domains\Organization\Rules;

use App\Domains\Organization\Models\Organization;
use Illuminate\Contracts\Validation\Rule;

class ActiveOrganizationMemberRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private Organization $organization)
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->organization->activeMembers()->wherePivot('user_id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.active_organization_member');
    }
}
