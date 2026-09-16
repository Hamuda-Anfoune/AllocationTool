<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\AcademicYear;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'users' => UserResource::collection(
                User::query()->withAccountType()->orderBy('users.account_type_id')
                    ->get(['users.name', 'users.account_type_id', 'users.email', 'users.active', 'users.created_at', 'account_types.account_type'])
            ),
        ]);
    }

    /**
     * Only super admins may view the full admin list.
     */
    public function showAllActiveAdmins(): JsonResponse
    {
        return response()->json([
            'admins' => UserResource::collection(User::admins()->active()->get(['email', 'name', 'created_at'])),
        ]);
    }

    public function showAllActiveTas(): JsonResponse
    {
        return response()->json([
            'tas' => UserResource::collection(
                User::query()->active()->tasAndGtas()->withAccountType()->get(['users.*', 'account_types.account_type'])
            ),
        ]);
    }

    public function showAllActiveConvenors(): JsonResponse
    {
        return response()->json([
            'convenors' => UserResource::collection(User::convenors()->active()->get(['email', 'name', 'created_at'])),
        ]);
    }

    public function showTasWithoutPrefsForYear(AcademicYear $academicYear): JsonResponse
    {
        return response()->json([
            'tas_without_preferences' => UserResource::collection(
                User::query()->active()->tasAndGtas()->withAccountType()->withoutTaPreferencesForYear($academicYear->year)
                    ->get(['users.email', 'users.name', 'users.account_type_id', 'account_types.account_type'])
            ),
        ]);
    }

    public function showConvenorsWithoutPrefsForYear(AcademicYear $academicYear): JsonResponse
    {
        $convenorsWithoutPrefs = Module::withoutPreferencesForYear($academicYear->year)
            ->select('convenor_email')->distinct()->get();

        $names = User::whereIn('email', $convenorsWithoutPrefs->pluck('convenor_email'))->pluck('name', 'email');

        foreach ($convenorsWithoutPrefs as $convenor) {
            $convenor->name = $names->get($convenor->convenor_email);
            $convenor->modules = Module::where('convenor_email', $convenor->convenor_email)
                ->withoutPreferencesForYear($academicYear->year)
                ->get();
        }

        return response()->json([
            'convenors_without_preferences' => $convenorsWithoutPrefs,
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
