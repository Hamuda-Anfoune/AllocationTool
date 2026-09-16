<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaPreferenceResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class TAController extends Controller
{
    /**
     * Show every preference a TA has submitted. Admins may view any TA; a TA may only view their own. Convenors may not view this.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'ta_preferences' => TaPreferenceResource::collection($user->taPreferences),
        ]);
    }
}
