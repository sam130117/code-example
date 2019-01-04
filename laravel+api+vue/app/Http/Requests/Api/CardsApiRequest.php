<?php

namespace App\Http\Requests;

use App\Models\Cards;
use Illuminate\Foundation\Http\FormRequest;

class CardsApiRequest extends FormRequest
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
                    'name'        => 'string',
                    'last_number' => 'string|unique:cards,last_number,' . $this->card,
                    'total_value' => 'numeric',
                    'card_type'   => 'in:' . Cards::TYPE_CREDIT . ',' . Cards::TYPE_DEBIT,
                    'close_date'  => 'date',
                    'case_id'     => 'nullable|exists:cases,id',
                ];
            default:
                return [
                    'name'        => 'required|string',
                    'last_number' => 'required|string|unique:cards,last_number',
                    'total_value' => 'required|numeric',
                    'card_type'   => 'required|in:' . Cards::TYPE_CREDIT . ',' . Cards::TYPE_DEBIT,
                    'close_date'  => 'required|date',
                    'case_id'     => 'nullable|exists:cases,id',
                ];
        }
    }
}
