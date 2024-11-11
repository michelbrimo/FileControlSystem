<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends Model
{
    use HasFactory;

    public function Files():BelongsTo{
        return $this->belongsTo(Files::class);
    }

    public function Users():BelongsTo{
        return $this->belongsTo(User::class);
    }
}
