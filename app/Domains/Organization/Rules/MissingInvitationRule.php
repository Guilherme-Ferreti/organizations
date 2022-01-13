<?php

namespace App\Domains\Organization\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Domains\Organization\Models\Invitation;
use App\Domains\Organization\Models\Organization;

class MissingInvitationRule implements Rule
{
    public function __construct(private Organization $organization)
    {

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
        return Invitation::query()
            ->where('organization_id', $this->organization->id)
            ->where('user_id', $value)
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.missing_invitation');
    }
}
