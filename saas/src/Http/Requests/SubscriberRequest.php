<?php

namespace Plugin\Saas\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class SubscriberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(Request $request)
    {
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:tl_users,email,' . $request->id . '|max:100',
            'password' => $request->id ? 'nullable|confirmed|min:6' : 'required|confirmed|min:6',
        ];

        return $rules;
    }

    /**
     * Customizing the error messages (optional).
     */
    public function messages()
    {
        return [
            'name.required' => 'The subscriber name is required.',
            'name.max' => 'The  name must not exceed 100 characters.',
            'email.required' => 'The email is required.',
            'email.max' => 'The email must not exceed 100 characters.',
            'email.unique' => 'The email already used.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'The password does not match.',
            'password.min' => 'The password minimum length 6.',
        ];
    }
}
