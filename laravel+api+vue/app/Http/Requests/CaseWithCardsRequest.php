<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaseWithCardsRequest extends FormRequest
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
        $cardRules = (new CardsRequest)->rules();
        $rules = [
            'title'               => 'required|string',
            'client_email'        => 'required|email|unique:cases,client_email,' . $this->id,
            'website'             => 'required|string',
            'country'             => 'required|string',
            'user_id'             => 'nullable|exists:users,id',
            'cards'               => 'nullable|array',
            'cards.*.name'        => $cardRules['name'],
            'cards.*.total_value' => $cardRules['total_value'],
            'cards.*.card_type'   => $cardRules['card_type'],
            'cards.*.close_date'  => $cardRules['close_date'],
        ];
        $cards = $this->cards;

        if ($cards)
            foreach ($cards as $key => $card)
                $rules['cards.' . $key . '.last_number'] = $cardRules['last_number'] . $card['id'];

        return $rules;
    }

    public function messages()
    {
        return [
            'cards.*.name.required' => 'The card name field is required.',
            'cards.*.total_value.required' => 'The card total value field is required.',
            'cards.*.last_number.required' => 'The card last number field is required.',
            'cards.*.card_type.required' => 'The card type field is required.',
            'cards.*.close_date.required' => 'The card close date field is required.',

            'cards.*.last_number.unique' => 'The card last number has already been taken.',
            'user_id.exists' => 'The selected agent is invalid.',
        ];
    }
}
