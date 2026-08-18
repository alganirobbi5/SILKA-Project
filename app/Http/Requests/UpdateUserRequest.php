<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'level' => ['required', Rule::in(['admin', 'bendahara'])],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:2048'],
        ];
    }

    public function messages()
    {
        return (new StoreUserRequest())->messages();
    }
}
