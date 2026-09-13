<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModulePriorityWeightsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'module_priority_weight_1' => ['required', 'integer'],
            'module_priority_weight_2' => ['required', 'integer'],
            'module_priority_weight_3' => ['required', 'integer'],
            'module_priority_weight_4' => ['required', 'integer'],
            'module_priority_weight_5' => ['required', 'integer'],
            'module_priority_weight_6' => ['required', 'integer'],
            'module_priority_weight_7' => ['required', 'integer'],
            'module_priority_weight_8' => ['required', 'integer'],
            'module_priority_weight_9' => ['required', 'integer'],
            'module_priority_weight_10' => ['required', 'integer'],
        ];
    }
}
