<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaPreferenceResource;
use App\Services\BasicDBClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TAController extends Controller
{
    public function __construct(protected BasicDBClass $basicDBClass) {}

    /**
     * Show every preference a TA has submitted. Admins may view any TA; a TA may only view their own. Convenors may not view this.
     */
    public function show(Request $request, string $email): JsonResponse
    {
        $accountTypeId = $request->user()->account_type_id;
        $isAdmin = in_array($accountTypeId, ['000', '001'], true);
        $isSelf = in_array($accountTypeId, ['003', '004'], true) && $request->user()->email === $email;

        if (! $isAdmin && ! $isSelf) {
            return response()->json(['message' => 'Sorry, only admins and teaching assistants can view this information.'], 403);
        }

        return response()->json([
            'ta_preferences' => TaPreferenceResource::collection($this->basicDBClass->getAllPrefsForTAEmail($email)),
        ]);
    }
}
