<?php

namespace Database\Seeders;

use App\Models\Auteur;
use App\Models\Emprunt;
use App\Models\Livre;
use App\Models\Membre;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //Auteurs et livres dans l'ordre du sujet avec id de 1 à 6
        $catalogue = [
            ['Cheikh Hamidou Kane',      'Sénégal',      'L\'Aventure ambiguë',              1961, 3, '978-0-000-00001-1'],
            ['Mariama Bâ',               'Sénégal',      'Une si longue lettre',             1979, 4, '978-0-000-00002-8'],
            ['Ousmane Sembène',          'Sénégal',      'Les Bouts de bois de Dieu',        1960, 2, '978-0-000-00003-5'],
            ['Aminata Sow Fall',         'Sénégal',      'La Grève des bàttu',              1979, 2, '978-0-000-00004-2'],
            ['Léopold Sédar Senghor',    'Sénégal',      'Chants d\'ombre',                  1945, 1, '978-0-000-00005-9'],
            ['Boubacar Boris Diop',      'Sénégal',      'Murambi, le livre des ossements',  2000, 2, '978-0-000-00006-6'],
        ];

        foreach ($catalogue as [$nomAuteur, $pays, $titre, $annee, $exemplaires, $isbn]) {
            $auteur = Auteur::create(['nom' => $nomAuteur, 'pays' => $pays]);

            Livre::create([
                'auteur_id'   => $auteur->id,
                'titre'       => $titre,
                'isbn'        => $isbn,
                'annee'       => $annee,
                'exemplaires' => $exemplaires,
                'disponibles' => $exemplaires, // au départ, tout est disponible
            ]);
        }

        //On crée 10 membres et le membre 1 est Awa Diop.
        $noms = [
            'Libasse Diop', 'Moussa Ba', 'Babacar Dieng', 'Ibrahima Fall', 'Bousso Diop',
            'Mohamed Yezid', 'Fafa Marietou', 'Mamadou Diasse', 'Ndeye Simal', 'Cheikh Mbaye',
        ];

        foreach ($noms as $i => $nom) {
            $numero = $i + 1;
            Membre::create([
                'nom'       => $nom,
                'email'     => "membre{$numero}@univ.biblio",
                'matricule' => sprintf('MAT%04d', $numero), // MAT0001, MAT0002...
            ]);
        }

        // 3. Quatre emprunts, dans l'ordre du sujet
        $this->emprunter(membreId: 1, livreId: 1);
        $this->emprunter(membreId: 2, livreId: 2);
        $this->emprunter(membreId: 3, livreId: 5);
        $this->emprunter(membreId: 4, livreId: 3, joursDepuisEmprunt: 20); // 20 j - 14 j = 6 j de retard
    }

    // Petite méthode privée pour ne pas répéter le code
    private function emprunter(int $membreId, int $livreId, int $joursDepuisEmprunt = 0): void
    {
        $emprunteLe = today()->subDays($joursDepuisEmprunt);

        Emprunt::create([
            'livre_id'     => $livreId,
            'membre_id'    => $membreId,
            'emprunte_le'  => $emprunteLe,
            'retour_prevu' => $emprunteLe->copy()->addDays(14),
        ]);

        // On garde disponibles cohérent : un exemplaire de moins
        Livre::whereKey($livreId)->decrement('disponibles');
    }
}