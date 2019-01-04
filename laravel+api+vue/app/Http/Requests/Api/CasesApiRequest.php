<?php

namespace App\Http\Requests;

use App\Models\Cards;
use Illuminate\Foundation\Http\FormRequest;

class CasesApiRequest extends FormRequest
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
                    'title'        => 'string',
                    'client_email' => 'email|unique:cases,client_email,' . $this->case,
                    'website'      => 'string',
                    'country'      => 'string',
                    'user_id'      => 'nullable|exists:users,id',
                ];
            default:
                return [
                    'title'        => 'required|string',
                    'client_email' => 'required|email|unique:cases,client_email',
                    'website'      => 'required|string',
                    'country'      => 'required|string',
                    'user_id'      => 'nullable|exists:users,id',
                ];
        }
    }
}
