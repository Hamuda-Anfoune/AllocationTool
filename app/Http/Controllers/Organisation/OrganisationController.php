<?php

namespace App\Http\Controllers\Organisation;

use App\Models\Organisation\Organisation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organisation\StoreOrganisationRequest;
use App\Http\Requests\Organisation\UpdateOrganisationRequest;
use App\Http\Resources\Organisation\OrganisationResource;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return all organisations.
        return OrganisationResource::collection(Organisation::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganisationRequest $request)
    {
        // get and store the new organisation
        $organisation = Organisation::create($request->validated());

        // return organisation details using organisation resource.
        return new OrganisationResource($organisation);
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation)
    {
        return new OrganisationResource($organisation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganisationRequest $request, Organisation $organisation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation)
    {
        //
    }
}
