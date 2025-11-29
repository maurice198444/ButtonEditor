<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreCardVersionRequest
 *
 * Validates incoming card version payloads.
 */
class StoreCardVersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'json' => ['required', 'array', 'required_array_keys:name,canvas,elements'],
        ];
    }
}
