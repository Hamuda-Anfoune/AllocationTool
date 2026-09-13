<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    public function store(StoreModuleRequest $request): JsonResponse
    {
        $module = Module::create($request->validated());

        return response()->json(['module' => new ModuleResource($module)], 201);
    }
}
