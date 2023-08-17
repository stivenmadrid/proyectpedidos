<?php

namespace App\Models\Producto;

use App\Models\Categoria\categoria;
use App\Models\Pedido\Pedido;
use App\Models\Adiciones\Adicion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'categoria_id'];

    public function categoria()
    {
        return $this->belongsTo(categoria::class);
    }

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_productos')
            ->withPivot('cantidad', 'observacion', 'cocina')
            ->withTimestamps();
    }
    
    
    
    public function adiciones()
    {
        return $this->belongsToMany(Adicion::class, 'adicion_productos')
            ->withPivot('cantidad');
    }
}
