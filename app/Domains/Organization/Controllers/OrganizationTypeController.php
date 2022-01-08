<?php

namespace App\Domains\Organization\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Domains\Organization\Models\OrganizationType;

class OrganizationTypeController extends Controller
{
    public function index()
    {
        $organization_types = Cache::remember('organization_types', Carbon::now()->addWeek(), fn () => 
            OrganizationType::all()
        );

        return $this->respondOk(compact('organization_types'));
    }
}
