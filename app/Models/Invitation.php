<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invitation extends Model
{
    use HasFactory;
    
    protected $fillable = ['admin_id', 'group_id', 'user_id'];

    public function Admin():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function Group():BelongsTo{
        return $this->belongsTo(Group::class);
    }

    public function User():HasOne{
        return $this->hasOne(User::class);
    }

    
}
