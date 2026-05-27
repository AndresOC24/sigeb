<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rostro extends Model
{
    protected $table = 'rostros';
    protected $fillable = ['user_id', 'descriptor'];
    protected $casts = ['descriptor' => 'array']; // face-api.js: 128 floats

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
