<?php

namespace App\Domains\Organization\Models;

use App\Models\User;
use App\Traits\Uuid;
use Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\Domains\Organization\OrganizationFactory;

class Organization extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $fillable = [
        'fantasy_name',
        'corporate_name',
        'domain',
        'cpf_cnpj',
        'logo',
        'social_contract',
        'organization_type_id',
        'interests',
    ];

    protected $casts = [
        'interests'             => 'array',
        'organization_type_id'  => 'integer',
    ];

    public function organization_type()
    {
        return $this->belongsTo(OrganizationType::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->using(OrganizationUser::class)
            ->withPivot([
                'is_technical_manager',
                'is_owner',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function activeMembers()
    {
        return $this->members()->wherePivot('is_active', true);
    }

    public function activeOwners()
    {
        return $this->activeMembers()->wherePivot('is_owner', true);
    }

    public function isActiveMember(int|User $user): bool
    {
        return $this->activeMembers()->get()->contains($user);
    }

    public function isActiveOwner(int|User $user): bool
    {
        return $this->activeOwners()->get()->contains($user);
    }

    public function addMember(int|User $user, array $attributes = []): void
    {
        $attributes = Arr::only($attributes, ['is_technical_manager', 'is_owner', 'is_active']);

        $this->members()->attach($user, $attributes);
    }

    public function updateMember(User $user, array $attributes): void
    {
        $attributes = Arr::only($attributes, ['is_technical_manager', 'is_owner', 'is_active']);

        $this->members()->updateExistingPivot($user, $attributes);
    }

    public function removeMember(int|User $user): void
    {
        $this->members()->detach($user);
    }

    public function transferOwnership(int|User $owner, int|User $new_owner): void
    {
        DB::beginTransaction();

        $this->activeOwners()->updateExistingPivot($owner, [
            'is_owner' => false,
        ]);
        
        $this->activeMembers()->updateExistingPivot($new_owner, [
            'is_owner' => true,
        ]);

        DB::commit();
    }

    protected static function newFactory()
    {
        return OrganizationFactory::new();
    }
}
