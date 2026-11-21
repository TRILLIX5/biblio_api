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
    Schema::create('livres', function (Blueprint $table) {
        $table->id();
        
        // clé étrangère vers auteurs. restrictOnDelete : impossible de supprimer un auteur qui a des livres.
        $table->foreignId('auteur_id')->constrained('auteurs')->restrictOnDelete();

        $table->string('titre')->index();          // index : on cherche par titre
        $table->string('isbn', 20)->unique();      // unique :on peut pas avoir 2 livres avec le même isbn
        $table->unsignedSmallInteger('annee')->nullable(); // 0 à 65535, suffisant pour une année
        $table->unsignedInteger('exemplaires')->default(1); // nombre total d'exemplaires
        $table->unsignedInteger('disponibles')->default(1); // nombre actuellement disponibles
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livres');
    }
};
