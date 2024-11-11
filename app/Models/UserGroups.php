<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGroups extends Model
{
    use HasFactory;

    public function User():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function Groups():BelongsTo{
        return $this->belongsTo(Groups::class);
    }
}
