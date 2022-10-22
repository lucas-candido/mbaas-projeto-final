<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
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
