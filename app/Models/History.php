<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends Model
{
    use HasFactory;

    public function File():BelongsTo{
        return $this->belongsTo(File::class);
    }

    public function Users():BelongsTo{
        return $this->belongsTo(User::class);
    }
}
