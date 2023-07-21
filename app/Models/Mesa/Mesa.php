<?php

namespace App\Models\Mesa;

use App\Models\Pedido\Pedido;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;
    protected $table = 'mesas';

    protected $fillable = ['nombre', 'capacidad', 'Estado', 'Descripcion'];

    public function pedidos(){
        return $this->hasMany(Pedido::class);
    }
}
