<?php

namespace App\Http\Controllers\Module;

use App\Models\Module\ModulePreference;
use App\Http\Controllers\Controller;
use App\Http\Requests\Module\StoreModulePreferenceRequest;
use App\Http\Requests\Module\UpdateModulePreferenceRequest;

class ModulePreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreModulePreferenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ModulePreference $modulePreference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModulePreference $modulePreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateModulePreferenceRequest $request, ModulePreference $modulePreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModulePreference $modulePreference)
    {
        //
    }
}
