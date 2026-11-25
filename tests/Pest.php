<?php

use App\Models\Auteur;
use App\Models\Emprunt;
use App\Models\Livre;
use App\Models\Membre;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Tous les tests du dossier tests/Feature :
// - héritent de TestCase 
// - remettent la base à zéro avant chaque test
pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

//Fonctions pour fabriquer des données 

// random_bytes() donne des valeurs vraiment uniques.
// (uniqid() peut renvoyer deux fois la même valeur sous Windows dans une boucle rapide.)
function creerLivre(int $exemplaires = 2, ?int $disponibles = null): Livre
{
    $auteur = Auteur::create(['nom' => 'Auteur Test']);

    return Livre::create([
        'auteur_id'   => $auteur->id,
        'titre'       => 'Livre ' . bin2hex(random_bytes(4)),
        'isbn'        => 'isbn-' . bin2hex(random_bytes(6)),
        'exemplaires' => $exemplaires,
        'disponibles' => $disponibles ?? $exemplaires, // ?? : "si nul, alors..."
    ]);
}

function creerMembre(): Membre
{
    $id = bin2hex(random_bytes(6));

    return Membre::create([
        'nom'       => 'Membre Test',
        'email'     => "m{$id}@test.local",
        'matricule' => "MAT{$id}",
    ]);
}

// $retourDansJours négatif => retour prévu dans le passé => emprunt EN RETARD
function creerEmprunt(Livre $livre, Membre $membre, int $retourDansJours = 14): Emprunt
{
    $livre->decrement('disponibles'); // on garde le stock cohérent
    $retourPrevu = today()->addDays($retourDansJours);

    return Emprunt::create([
        'livre_id'     => $livre->id,
        'membre_id'    => $membre->id,
        'emprunte_le'  => $retourPrevu->copy()->subDays(14),
        'retour_prevu' => $retourPrevu,
    ]);
}