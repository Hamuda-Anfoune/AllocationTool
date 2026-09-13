<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\BasicDBClass;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(protected BasicDBClass $basicDBClass) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'users' => UserResource::collection($this->basicDBClass->getAllRegisteredUsers()),
        ]);
    }

    /**
     * Only super admins may view the full admin list.
     */
    public function showAllActiveAdmins(): JsonResponse
    {
        return response()->json([
            'admins' => UserResource::collection($this->basicDBClass->getAllactiveAdmins()),
        ]);
    }

    public function showAllActiveTas(): JsonResponse
    {
        return response()->json([
            'tas' => UserResource::collection($this->basicDBClass->getAllActiveTas()),
        ]);
    }

    public function showAllActiveConvenors(): JsonResponse
    {
        return response()->json([
            'convenors' => UserResource::collection($this->basicDBClass->getAllActiveConvenors()),
        ]);
    }

    public function showTasWithoutPrefsForYear(AcademicYear $academicYear): JsonResponse
    {
        return response()->json([
            'tas_without_preferences' => UserResource::collection($this->basicDBClass->getActiveTasWithoutPrefsForYear($academicYear->year)),
        ]);
    }

    public function showConvenorsWithoutPrefsForYear(AcademicYear $academicYear): JsonResponse
    {
        return response()->json([
            'convenors_without_preferences' => $this->basicDBClass->getConvenorsWithoutPrefsWithModulesForYear($academicYear->year),
        ]);
    }

    /**
     * Delete a user. Fails with 409 if the user already has related allocation data (FK constraint).
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'Sorry, this user has already submitted data and cannot be deleted before deleting all related data.',
            ], 409);
        }

        return response()->json(['message' => 'User deleted.']);
    }
}
