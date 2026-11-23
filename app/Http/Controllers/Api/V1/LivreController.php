<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\RegleMetierException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListeLivresRequest;
use App\Http\Requests\StoreLivreRequest;
use App\Http\Requests\UpdateLivreRequest;
use App\Http\Resources\LivreResource;
use App\Models\Livre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LivreController extends Controller
{
    // GET /api/v1/livres?q=&disponible=1
    public function index(ListeLivresRequest $request): AnonymousResourceCollection
    {
        $recherche = $request->validated('q'); // validated() : uniquement les données qui ont passé la validation

        $livres = Livre::with('auteur') // eager loading  charge tous les auteurs en 1 seule requête
            // when(condition, callback) n'ajoute le filtre que si la condition est vraie
            ->when($recherche, function ($requete) use ($recherche) {
                $requete->where(function ($sous) use ($recherche) {
                    $sous->where('titre', 'like', "%{$recherche}%")
                         ->orWhereHas('auteur', fn ($a) => $a->where('nom', 'like', "%{$recherche}%"));
                });
            })
            ->when($request->boolean('disponible'), fn ($requete) => $requete->where('disponibles', '>', 0))
            ->orderBy('id')
            ->paginate(10); // 10 par page

        return LivreResource::collection($livres);
    }

    // GET /api/v1/livres/{livre}  (404 automatique si l'id n'existe pas)
    public function show(Livre $livre): LivreResource
    {
        return new LivreResource($livre->load('auteur'));
    }

    // POST /api/v1/livres
    public function store(StoreLivreRequest $request): JsonResponse
    {
        $donnees = $request->validated();

        // Au départ, tous les exemplaires sont disponibles. C'est le SERVEUR qui décide.
        $donnees['disponibles'] = $donnees['exemplaires'];

        $livre = Livre::create($donnees);

        return (new LivreResource($livre->load('auteur')))
            ->response()
            ->setStatusCode(201); // 201 Created
    }

    // PATCH /api/v1/livres/{livre}
    public function update(UpdateLivreRequest $request, Livre $livre): LivreResource
    {
        $livre->update($request->validated());

        return new LivreResource($livre->load('auteur'));
    }

    // DELETE /api/v1/livres/{livre}
    public function destroy(Livre $livre): JsonResponse
    {
        // Règle 6 : on ne supprime pas un livre déjà emprunté (même s'il est rendu depuis)
        if ($livre->emprunts()->exists()) {
            throw new RegleMetierException('livre_utilise', 'Ce livre a déjà été emprunté, il ne peut pas être supprimé.');
        }

        $livre->delete();

        return response()->json(null, 204); // 204: succès
    }
}