<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmpruntResource;
use App\Models\Membre;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MembreController extends Controller
{
    // GET /api/v1/membres/{membre}/emprunts
    public function emprunts(Membre $membre): AnonymousResourceCollection
    {
        $emprunts = $membre->emprunts()
            ->with('livre.auteur')
            ->latest('id')       // les plus récents d'abord
            ->paginate(10);

        return EmpruntResource::collection($emprunts);
    }
}