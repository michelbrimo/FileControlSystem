<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'admin_id',
    ];

    public function File():HasMany{
        return $this->hasMany(File::class);
    }

    public function UserGroup():HasMany{
        return $this->hasMany(UserGroup::class);
    }

    public function Admin():HasOne{
        return $this->hasOne(User::class);
    }

    public function Invitation():HasMany{
        return $this->hasMany(Invitation::class);
    }
}
