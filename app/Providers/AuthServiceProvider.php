<?php

namespace App\Providers;

use App\Models\Invitation;
use Illuminate\Support\Facades\Gate;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Policies\InvitationPolicy;
use App\Domains\Organization\Policies\OrganizationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Organization::class => OrganizationPolicy::class,
        Invitation::class => InvitationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
