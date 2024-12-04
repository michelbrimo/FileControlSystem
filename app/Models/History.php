<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'user_id',
        'link',
        'description'
    ];

    public function File():BelongsTo{
        return $this->belongsTo(File::class);
    }

    public function Users():BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }
}
