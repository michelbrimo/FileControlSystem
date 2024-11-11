<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileCheck extends Model
{
    use HasFactory;

    public function User():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function File():BelongsTo{
        return $this->belongsTo(File::class);
    }
}
