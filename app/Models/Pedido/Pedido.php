<?php

namespace App\Models\Pedido;

use App\Models\Mesa\Mesa;
use App\Models\Producto\Producto;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['estado', 'total'];

    public $timestamps = false;

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'pedido_productos')
            ->withPivot('cantidad', 'observacion')
            ->withTimestamps();
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
