<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUniversityUserRequest;
use App\Http\Resources\UniversityUserResource;
use App\Models\UniversityUser;
use Illuminate\Http\JsonResponse;

class UniversityUsersController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'university_users' => UniversityUserResource::collection(
                UniversityUser::query()->withAccountType()->orderBy('university_users.account_type_id')
                    ->get(['university_users.email', 'university_users.account_type_id', 'university_users.created_at', 'account_types.account_type'])
            ),
        ]);
    }

    public function store(StoreUniversityUserRequest $request): JsonResponse
    {
        $universityUser = UniversityUser::create($request->validated());

        return response()->json(['university_user' => new UniversityUserResource($universityUser)], 201);
    }
}
