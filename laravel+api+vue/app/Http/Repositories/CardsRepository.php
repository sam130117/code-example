<?php

namespace App\Http\Repositories;

use App\Models\Cards;
use Illuminate\Pagination\LengthAwarePaginator;

class CardsRepository extends BaseRepository
{
    function model(): string
    {
        return Cards::class;
    }

    public function getAll(): LengthAwarePaginator
    {
        $search = request('search', null);
        $cardType = request('cardType', null);
        $cards = $this->model::select('id', 'name', 'last_number', 'total_value', 'card_type', 'close_date', 'case_id');

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
