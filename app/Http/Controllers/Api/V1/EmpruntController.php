<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmpruntRequest;
use App\Http\Resources\EmpruntResource;
use App\Models\Emprunt;
use App\Services\EmpruntService;
use Illuminate\Http\JsonResponse;

class EmpruntController extends Controller
{
    public function store(StoreEmpruntRequest $request, EmpruntService $service): JsonResponse
    {
        $emprunt = $service->emprunter(
            $request->validated('livre_id'),
            $request->validated('membre_id'),
        );

        return (new EmpruntResource($emprunt))->response()->setStatusCode(201);
    }

    // POST /api/v1/emprunts/{emprunt}/retour
    public function retour(Emprunt $emprunt, EmpruntService $service): EmpruntResource
    {
        return new EmpruntResource($service->rendre($emprunt));
    }
}