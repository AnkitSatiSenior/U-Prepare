<?php

namespace Laravel\Fortify\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Fortify;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Here we change to accept 'login' (email or username).
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login' => 'required|string',  // changed from Fortify::username() to 'login'
            'password' => 'required|string',
        ];
    }

    /**
     * Override the username method to use 'login' as the input name.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }
}
