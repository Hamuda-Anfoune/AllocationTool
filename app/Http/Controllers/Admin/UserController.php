<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\AcademicYear;
use App\Models\User;
use App\Services\BasicDBClass;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function showAllActiveAdmins(Request $request): JsonResponse
    {
        if ($request->user()->account_type_id !== '000') {
            return response()->json(['message' => 'Sorry, only super admins can view this information.'], 403);
        }

        return response()->json([
            'admins' => UserResource::collection($this->basicDBClass->getAllactiveAdmins()),
        ]);
    }

    public function showAllActiveTas(Request $request): JsonResponse
    {
        if ($forbidden = $this->requireAdmin($request)) {
            return $forbidden;
        }

        return response()->json([
            'tas' => UserResource::collection($this->basicDBClass->getAllActiveTas()),
        ]);
    }

    public function showAllActiveConvenors(Request $request): JsonResponse
    {
        if ($forbidden = $this->requireAdmin($request)) {
            return $forbidden;
        }

        return response()->json([
            'convenors' => UserResource::collection($this->basicDBClass->getAllActiveConvenors()),
        ]);
    }

    public function showTasWithoutPrefsForYear(Request $request, AcademicYear $academicYear): JsonResponse
    {
        if ($forbidden = $this->requireAdmin($request)) {
            return $forbidden;
        }

        return response()->json([
            'tas_without_preferences' => UserResource::collection($this->basicDBClass->getActiveTasWithoutPrefsForYear($academicYear->year)),
        ]);
    }

    public function showConvenorsWithoutPrefsForYear(Request $request, AcademicYear $academicYear): JsonResponse
    {
        if ($forbidden = $this->requireAdmin($request)) {
            return $forbidden;
        }

        return response()->json([
            'convenors_without_preferences' => $this->basicDBClass->getConvenorsWithoutPrefsWithModulesForYear($academicYear->year),
        ]);
    }

    /**
     * Only super admins and admins (000/001) may view this information.
     */
    protected function requireAdmin(Request $request): ?JsonResponse
    {
        if (in_array($request->user()->account_type_id, ['000', '001'], true)) {
            return null;
        }

        return response()->json(['message' => 'Sorry, only admins can view this information.'], 403);
    }

    /**
     * Delete a user. Fails with 409 if the user already has related allocation data (FK constraint).
     */
    public function destroy(string $email): JsonResponse
    {
        try {
            User::where('email', $email)->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'Sorry, this user has already submitted data and cannot be deleted before deleting all related data.',
            ], 409);
        }

        return response()->json(['message' => 'User deleted.']);
    }
}
