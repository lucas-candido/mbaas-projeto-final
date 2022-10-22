<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $connection = 'pgsql';

    protected $fillable = [
        'produto', 'nome',
        'quantidade', 'preco', 'tamanho',
        'cor', 'inativo', 'marca_id',
        'categoria_id', 'imagem', 'destaque',
        'preco_promocional', 'descricao'
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
