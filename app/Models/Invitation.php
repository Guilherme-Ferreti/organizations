<?php

namespace App\Models;

use Wildside\Userstamps\Userstamps;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory, BelongsToOrganization, Userstamps;

    protected $fillable = [
        'user_id',
    ];
}
