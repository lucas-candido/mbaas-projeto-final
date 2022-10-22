<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $connection = 'pgsql';

    protected $fillable = [
        'user_id', 'pagamento', 'obs',
        'parcelas', 'status', 'motivocancel', 'cancelusu',
        'cancelempresa', 'vrfrete'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(ItemPedido::class)->with('produto')->orderBy('item_pedidos.id');
    }

}
