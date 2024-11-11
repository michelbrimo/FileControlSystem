<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groups extends Model
{
    use HasFactory;

    public function Files():HasMany{
        return $this->hasMany(Files::class);
    }

    public function UserGroups():HasMany{
        return $this->hasMany(UserGroups::class);
    }

    public function Admin():HasOne{
        return $this->hasOne(User::class);
    }
}
