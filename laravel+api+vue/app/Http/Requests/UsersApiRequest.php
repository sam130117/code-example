<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersApiRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'PUT':
                return [
                    'name'  => 'string',
                    'email' => 'email|unique:users,email,' . $this->user,
                ];
            default:
                return [
                    'name'             => 'required|string',
                    'email'            => 'required|email|unique:users,email',
                    'password'         => 'required|string|min:8',
                    'password_confirm' => 'required|same:password',
                ];
        }
    }
}
