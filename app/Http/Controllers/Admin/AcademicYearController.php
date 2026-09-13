<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;

class AcademicYearController extends Controller
{
    /**
     * Set the current academic year, clearing the "current" flag from every other year.
     */
    public function update(UpdateAcademicYearRequest $request): JsonResponse
    {
        $data = $request->validated();

        AcademicYear::query()->update(['current' => false]);
        AcademicYear::where('year', $data['new_academic_year'])->update(['current' => true]);

        return response()->json(['message' => "Current academic year updated to {$data['new_academic_year']}."]);
    }
}
