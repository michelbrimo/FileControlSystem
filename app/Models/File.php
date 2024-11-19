<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class File extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'file_name',
        'file_path',
        'state',
        'group_id',
    ];

    
    public function Group():BelongsTo{
        return $this->belongsTo(Group::class);
    }

    public function History():HasMany{
        return $this->hasMany(History::class);
    }

    public function FileCheck():HasMany{
        return $this->hasMany(FileCheck::class);
    }
}
