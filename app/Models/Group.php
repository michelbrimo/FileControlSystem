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

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function File():HasMany{
        return $this->hasMany(File::class);
    }

    public function UserGroup():HasMany{
        return $this->hasMany(UserGroup::class);
    }

    public function Admin(){
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function Invitation():HasMany{
        return $this->hasMany(Invitation::class);
    }

}
