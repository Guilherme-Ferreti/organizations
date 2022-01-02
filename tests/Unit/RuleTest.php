<?php

namespace Tests\Unit;

use App\Domains\Organization\Models\Interest;
use App\Domains\Organization\Rules\ExistingInterestName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RuleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        Artisan::call('db:seed');
    }

    public function test_rule_existing_interest_name_works_correctly()
    {
        $rules = ['interest_name' => new ExistingInterestName];
        $data = ['interest_name' => Interest::inRandomOrder()->first()->name];

        $this->assertFalse(Validator::make($data, $rules)->fails());

        $data['interest_name'] = 'invalid name';
    
        $this->assertTrue(Validator::make($data, $rules)->fails());
    }
}
