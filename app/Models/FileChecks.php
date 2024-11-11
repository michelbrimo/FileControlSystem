<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileChecks extends Model
{
    use HasFactory;

    public function User():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function Files():BelongsTo{
        return $this->belongsTo(Files::class);
    }
}
