<?php

namespace App\Domains\Organization\Rules;

use App\Domains\Organization\Models\Organization;
use Illuminate\Contracts\Validation\Rule;

class NonOrganizationMemberRule implements Rule
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
        return $this->organization->members()->wherePivot('user_id', $value)->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.non_organization_member');
    }
}
