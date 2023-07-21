<?php

namespace App\Models\Pagos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pago extends Model
{
    protected $fillable = [
        'pedido_id',
        'monto',
        'estado',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
