<?php

namespace App\Models\Categoria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto\Producto;

class categoria extends Model
{
    use HasFactory;

    protected $fillable = ['Nombre_Categoria'];

    // Relación con los productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
