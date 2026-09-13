<?php

namespace App\Http\Controllers\Prefs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prefs\StoreTaPreferenceRequest;
use App\Http\Requests\Prefs\UpdateTaPreferenceRequest;
use App\Http\Resources\TaPreferenceResource;
use App\Models\TaPreference;
use App\Services\AllocationsClass;
use App\Services\BasicDBClass;
use App\Services\PrefsClass;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TAController extends Controller
{
    public function __construct(
        protected BasicDBClass $basicDBClass,
        protected AllocationsClass $allocationsClass,
        protected PrefsClass $prefsClass,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $currentAcademicYear = $this->basicDBClass->getCurrentAcademicYear();

        $preferences = TaPreference::where('ta_email', $request->user()->email)
            ->where('academic_year', $currentAcademicYear)
            ->get();

        return response()->json(['ta_preferences' => TaPreferenceResource::collection($preferences)]);
    }

    public function store(StoreTaPreferenceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $email = $request->user()->email;
        $usernameNotWholeEmail = substr($email, 0, strpos($email, '@'));
        $preferenceId = $usernameNotWholeEmail.'Y'.$data['academic_year'];

        if ($this->prefsClass->taPreferenceExists($preferenceId)) {
            return response()->json([
                'message' => 'Sorry, seems like you have already submitted your preferences for this semester.',
            ], 409);
        }

        try {
            $this->prefsClass->storeTaPreferences($data, $email, $preferenceId);
        } catch (QueryException) {
            return response()->json(['message' => 'Error saving the preferences, please try submitting again later.'], 500);
        }

        return response()->json(['preference_id' => $preferenceId, 'message' => 'Preference stored.'], 201);
    }

    public function show(Request $request, TaPreference $taPreference): JsonResponse
    {
        if ($forbidden = $this->authorizeAccessTo($request, $taPreference)) {
            return $forbidden;
        }

        return response()->json([
            'ta_preference' => new TaPreferenceResource($taPreference),
            'module_choices' => $this->basicDBClass->getModuleChoicesForTAForYear($taPreference->preference_id),
            'language_choices' => $this->basicDBClass->getTaLanguageChoicesForPreference($taPreference->preference_id),
        ]);
    }

    public function update(UpdateTaPreferenceRequest $request, TaPreference $taPreference): JsonResponse
    {
        if ($forbidden = $this->authorizeAccessTo($request, $taPreference)) {
            return $forbidden;
        }

        $data = $request->validated();

        if ($this->basicDBClass->getCurrentAcademicYear() !== $data['academic_year']) {
            return response()->json(['message' => 'Sorry, only the current semester\'s preferences can be edited.'], 422);
        }

        if ($this->allocationsClass->allocationExistsForYear($data['academic_year'])) {
            return response()->json(['message' => 'Sorry, cannot edit these preferences — TA roles have already been allocated.'], 409);
        }

        $preferenceId = $taPreference->preference_id;
        $email = $taPreference->ta_email;

        $this->prefsClass->clearTaLanguageChoices($preferenceId);
        $this->prefsClass->clearTaModuleChoices($preferenceId);
        $this->prefsClass->clearTaPreference($preferenceId);

        try {
            $this->prefsClass->storeTaPreferences($data, $email, $preferenceId);
        } catch (QueryException) {
            return response()->json(['message' => 'Error updating your preferences, please try submitting again later.'], 500);
        }

        return response()->json(['message' => 'Your preferences have been updated.']);
    }

    public function destroy(Request $request, TaPreference $taPreference): JsonResponse
    {
        if ($forbidden = $this->authorizeAccessTo($request, $taPreference)) {
            return $forbidden;
        }

        if ($this->allocationsClass->allocationExistsForYear($taPreference->academic_year)) {
            return response()->json(['message' => 'Cannot delete preferences — TA roles have already been allocated for this semester.'], 409);
        }

        $preferenceId = $taPreference->preference_id;

        $this->prefsClass->clearTaLanguageChoices($preferenceId);
        $this->prefsClass->clearTaModuleChoices($preferenceId);
        $this->prefsClass->clearTaPreference($preferenceId);

        return response()->json(['message' => 'Your preferences have been deleted.']);
    }

    /**
     * Admins may access any TA preference; a TA/GTA may only access their own. Convenors may not access this at all.
     */
    protected function authorizeAccessTo(Request $request, TaPreference $taPreference): ?JsonResponse
    {
        $accountTypeId = $request->user()->account_type_id;

        if (in_array($accountTypeId, ['000', '001'], true)) {
            return null;
        }

        if (in_array($accountTypeId, ['003', '004'], true) && $request->user()->email === $taPreference->ta_email) {
            return null;
        }

        return response()->json(['message' => 'You are not authorized to access this preference.'], 403);
    }
}
