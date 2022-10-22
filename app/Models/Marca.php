<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $connection = 'pgsql';

    protected $fillable = [
        'nome', 'pai', 'imagem', 'principal'
    ];

    public function produto()
    {
        return $this->hasMany(Produto::class);
    }
}
