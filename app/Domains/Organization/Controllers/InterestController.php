<?php

namespace App\Domains\Organization\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Domains\Organization\Models\Interest;

class InterestController extends Controller
{
    public function index()
    {
        $interests = Cache::remember('interests', Carbon::now()->addWeek(), fn () => 
            Interest::all()
        );

        return $this->respondOk([
            'interests' => $interests->pluck('name'),
        ]);
    }
}
