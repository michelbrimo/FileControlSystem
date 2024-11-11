<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Files extends Model
{
    use HasFactory;

    public function Groups():BelongsTo{
        return $this->belongsTo(Groups::class);
    }

    public function History():HasMany{
        return $this->hasMany(History::class);
    }

    public function FileChecks():HasMany{
        return $this->hasMany(FileChecks::class);
    }
}
