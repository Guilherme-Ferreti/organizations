<?php

namespace App\Domains\Organization\Rules;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Validation\Rule;
use App\Domains\Organization\Models\Interest;

class ExistingInterestName implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $interests = Cache::remember('interests', Carbon::now()->addWeek(), fn () => 
            Interest::all()
        );

        return $interests->contains('name', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.existing_interest_name');
    }
}
