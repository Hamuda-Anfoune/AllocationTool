<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUniversityRequest;
use App\Http\Resources\UserResource;
use App\Models\University;
use App\Models\UniversityUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UniversityController extends Controller
{
    /**
     * Register a new university, its first university-user record, and its first super-admin user account.
     */
    public function store(StoreUniversityRequest $request): JsonResponse
    {
        $data = $request->validated();

        University::create([
            'name' => $data['university_name'],
            'university_email' => $data['university_email'],
        ]);

        UniversityUser::create([
            'email' => $data['email'],
            'account_type_id' => '000',
        ]);

        $user = User::create([
            'name' => $data['user_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'account_type_id' => '000',
        ]);

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }
}
