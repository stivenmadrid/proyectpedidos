<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('adicion_productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('adicion_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad');
            $table->boolean('cocina')->default(false); // Agregar la columna para indicar si va a cocina o no
    
            $table->timestamps();

            $table->foreign('adicion_id')->references('id')->on('adiciones')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adicion_productos');
    }
};
