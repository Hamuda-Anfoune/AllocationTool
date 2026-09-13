<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguageWeightsRequest extends FormRequest
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
            'language_weight_1' => ['required', 'integer'],
            'language_weight_2' => ['required', 'integer'],
            'language_weight_3' => ['required', 'integer'],
            'language_weight_4' => ['required', 'integer'],
            'language_weight_5' => ['required', 'integer'],
        ];
    }
}
