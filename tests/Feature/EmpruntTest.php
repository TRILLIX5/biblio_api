<?php

it('refuse l\'emprunt quand il ne reste aucun exemplaire (règle 2)', function () {
    // Arrange : un livre à 1 exemplaire dont 0 disponible et un membre
    $livre = creerLivre(exemplaires: 1, disponibles: 0);
    $membre = creerMembre();

    // Act + Assert : on appelle l'API et on vérifie statut et code
    $this->postJson('/api/v1/emprunts', ['livre_id' => $livre->id, 'membre_id' => $membre->id])
        ->assertStatus(409)
        ->assertJson(['code' => 'livre_indisponible']);
});

//Règle 1 : un livre a un auteur et un nombre d'exemplaires

it('exige un auteur et un nombre d\'exemplaires pour créer un livre (règle 1)', function () {
    $this->postJson('/api/v1/livres', ['titre' => 'T', 'isbn' => 'R1'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['auteur_id', 'exemplaires']);
});

it('renvoie le livre avec son auteur (règle 1)', function () {
    $livre = creerLivre();

    $this->getJson("/api/v1/livres/{$livre->id}")
        ->assertOk()
        ->assertJsonPath('data.auteur.nom', 'Auteur Test');
});

//Règle 2 : il faut un exemplaire disponible 

it('crée l\'emprunt et décrémente les disponibles (règle 2)', function () {
    $livre = creerLivre(exemplaires: 2);
    $membre = creerMembre();

    $this->postJson('/api/v1/emprunts', ['livre_id' => $livre->id, 'membre_id' => $membre->id])
        ->assertStatus(201)
        ->assertJsonPath('data.en_retard', false)
        ->assertJsonPath('data.rendu_le', null);

    expect($livre->fresh()->disponibles)->toBe(1); // fresh() relit la base
});

// Règle 3 : maximum 3 emprunts en cours

it('refuse un 4e emprunt en cours (règle 3)', function () {
    $membre = creerMembre();
    foreach (range(1, 3) as $i) {
        creerEmprunt(creerLivre(), $membre);
    }
    $quatrieme = creerLivre();

    $this->postJson('/api/v1/emprunts', ['livre_id' => $quatrieme->id, 'membre_id' => $membre->id])
        ->assertStatus(409)
        ->assertJson(['code' => 'limite_atteinte']);
});

// Règle 4 : emprunt en retard => interdit d'emprunter

it('refuse d\'emprunter si le membre a un emprunt en retard (règle 4)', function () {
    $membre = creerMembre();
    creerEmprunt(creerLivre(), $membre, retourDansJours: -6); // en retard de 6 jours
    $autre = creerLivre();

    $this->postJson('/api/v1/emprunts', ['livre_id' => $autre->id, 'membre_id' => $membre->id])
        ->assertStatus(409)
        ->assertJson(['code' => 'membre_en_retard']);
});

//Règle 5 : 14 jours, et un seul retour

it('fixe le retour prévu à 14 jours (règle 5)', function () {
    $livre = creerLivre();
    $membre = creerMembre();

    $this->postJson('/api/v1/emprunts', ['livre_id' => $livre->id, 'membre_id' => $membre->id])
        ->assertStatus(201)
        ->assertJsonPath('data.retour_prevu', today()->addDays(14)->toDateString());
});

it('rend un livre une seule fois et remet le stock (règle 5)', function () {
    $livre = creerLivre(exemplaires: 1);
    $membre = creerMembre();
    $emprunt = creerEmprunt($livre, $membre); // disponibles passe à 0

    $this->postJson("/api/v1/emprunts/{$emprunt->id}/retour")->assertStatus(200);
    expect($livre->fresh()->disponibles)->toBe(1);

    // Deuxième retour :refusé, et le stock ne bouge plus
    $this->postJson("/api/v1/emprunts/{$emprunt->id}/retour")
        ->assertStatus(409)
        ->assertJson(['code' => 'deja_rendu']);
    expect($livre->fresh()->disponibles)->toBe(1);
});

//Règle 6:pas de suppression d'un livre déjà emprunté

it('refuse de supprimer un livre déjà emprunté (règle 6)', function () {
    $livre = creerLivre();
    creerEmprunt($livre, creerMembre());

    $this->deleteJson("/api/v1/livres/{$livre->id}")
        ->assertStatus(409)
        ->assertJson(['code' => 'livre_utilise']);

    expect($livre->fresh())->not->toBeNull(); // il existe toujours
});

it('supprime un livre jamais emprunté', function () {
    $livre = creerLivre();

    $this->deleteJson("/api/v1/livres/{$livre->id}")->assertNoContent();
});

//Erreurs transverses 

it('renvoie 404 au format imposé pour une ressource inconnue', function () {
    $this->getJson('/api/v1/livres/999')
        ->assertStatus(404)
        ->assertJson(['code' => 'introuvable']);
});

it('renvoie 422 si le corps d\'un emprunt est invalide', function () {
    $this->postJson('/api/v1/emprunts', [])->assertStatus(422);
});

it('interdit au client d\'envoyer "disponibles" (cohérence)', function () {
    $auteur = \App\Models\Auteur::create(['nom' => 'A']);

    $this->postJson('/api/v1/livres', [
        'auteur_id' => $auteur->id, 'titre' => 'T', 'isbn' => 'X1',
        'exemplaires' => 2, 'disponibles' => 99,
    ])->assertStatus(422);
});

it('crée un livre avec disponibles égal aux exemplaires', function () {
    $auteur = \App\Models\Auteur::create(['nom' => 'A']);

    $this->postJson('/api/v1/livres', [
        'auteur_id' => $auteur->id, 'titre' => 'T', 'isbn' => 'X2', 'exemplaires' => 3,
    ])->assertStatus(201)->assertJsonPath('data.disponibles', 3);
});