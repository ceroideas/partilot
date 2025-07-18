<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateManager extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            // "user_id" => "required|string|max:255",
            // "image" => "required|string|max:255",
            "name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "last_name2" => "required|string|max:255",
            "nif_cif" => "required|string|max:255",
            "birthday" => "required|string|max:255",
            "email" => "required|string|max:255",
            "phone" => "required|string|max:255",
            "comment" => "required|string|max:255",
        ];
    }
}
