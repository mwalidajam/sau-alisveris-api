<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('super-admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'string',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password',
            'sections' => 'array',
        ];
    }
}
