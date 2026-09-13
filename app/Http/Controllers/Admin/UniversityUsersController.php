<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUniversityUserRequest;
use App\Http\Resources\UniversityUserResource;
use App\Models\UniversityUser;
use App\Services\BasicDBClass;
use Illuminate\Http\JsonResponse;

class UniversityUsersController extends Controller
{
    public function __construct(protected BasicDBClass $basicDBClass) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'university_users' => UniversityUserResource::collection($this->basicDBClass->getAllUniversityUsers()),
        ]);
    }

    public function store(StoreUniversityUserRequest $request): JsonResponse
    {
        $universityUser = UniversityUser::create($request->validated());

        return response()->json(['university_user' => new UniversityUserResource($universityUser)], 201);
    }
}
