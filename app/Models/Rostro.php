<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rostro extends Model
{
    protected $table = 'rostros';

    protected $fillable = ['user_id', 'descriptor'];

    protected $casts = [
        'descriptor' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}