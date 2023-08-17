<?php

namespace App\Models\Adiciones;

use Illuminate\Database\Eloquent\Model;

class Adicion extends Model
{
    protected $table = 'adiciones';
    protected $fillable = ['cantidad', 'cocina', 'observacion'];

    // Relación con Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación con Productos (relación many-to-many)
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'adicion_productos')
            ->withPivot('cantidad')
            ->withTimestamps();
    }
}

