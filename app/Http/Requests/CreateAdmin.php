<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdmin extends FormRequest
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
            "web"=>"nullable|string|max:255",
            // "image"=>"required|string|max:255",
            "name"=>"required|string|max:255",
            "receiving"=>"required|string|max:255",
            "society"=>"required|string|max:255",
            "nif_cif"=>"required|string|max:255",
            "province"=>"required|string|max:255",
            "city"=>"required|string|max:255",
            "postal_code"=>"required|string|max:255",
            "address"=>"required|string|max:255",
            "email"=>"required|string|max:255",
            "phone"=>"required|string|max:255",
            // "account"=>"required|string|max:255",
        ];
    }
}
