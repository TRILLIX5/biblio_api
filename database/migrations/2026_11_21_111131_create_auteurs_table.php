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
        Schema::create('auteurs', function (Blueprint $table) {
            $table->id();  //id :entier autoincrémenté,clé primaire
            $table->string('nom'); //oblig
            $table->string('pays')->nullable(); //nullable donc c'est facultatif
            $table->timestamps(); 
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('auteurs');
    }
};
