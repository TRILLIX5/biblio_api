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
        Schema::create('emprunts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livre_id')->constrained('livres')->restrictOnDelete();
            $table->foreignId('membre_id')->constrained('membres')->restrictOnDelete();
            

            $table->date('emprunte_le');  // date de l'emprunt(sans l'heure)
            $table->date('retour_prevu');
            $table->date('rendu_le')->nullable();  // NULL: si l'emprunt est encore en cours
            $table->timestamps();

            // Index composite : "les emprunts en cours d'un membre" est la requête la plus fréquente
            $table->index(['membre_id', 'rendu_le']);

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emprunts');
    }
};
