<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Http\Resources\SpaceResource;
use Illuminate\Http\JsonResponse;

class SpaceController extends Controller
{
    public function index(): JsonResponse
    {
        $spaces = Space::all();
        return response()->json(SpaceResource::collection($spaces));
    }

    public function show(Space $space): JsonResponse
    {
        return response()->json(new SpaceResource($space));
    }
}
