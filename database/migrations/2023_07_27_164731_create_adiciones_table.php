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
        Schema::create('adiciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->integer('cantidad');
            $table->boolean('cocina')->default(true);
            $table->string('observacion')->nullable(); // Esta columna debe estar presente
            // Agrega aquí los campos adicionales que necesites para la adición
            $table->timestamps();

            $table->foreign('pedido_id')->references('id')->on('pedidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adiciones');
    }
};
