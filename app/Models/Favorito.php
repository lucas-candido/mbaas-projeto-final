<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $connection = 'pgsql';

    protected $fillable = [
        'user_id', 'produto_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
