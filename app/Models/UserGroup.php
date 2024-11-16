<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
    ];

    public function User():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function Group():BelongsTo{
        return $this->belongsTo(Group::class);
    }
}
