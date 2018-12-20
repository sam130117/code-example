<?php

namespace App\Http\Services;

use App\Models\Cards;

class CardsService extends BaseService
{
    const MODEL_NAME = Cards::class;

    public function getAll()
    {
        $search = request('search', null);
        $cardType = request('cardType', null);
        $cards = Cards::select('id', 'name', 'last_number', 'total_value', 'card_type', 'close_date', 'case_id');

        if ($search)
            $cards->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%");
                $query->orWhere('last_number', 'LIKE', "%$search%");
            });

        if ($cardType)
            $cards->where('card_type', $cardType);

        $cards->orderBy('name', 'asc');
        return $cards->paginate();
    }

}
