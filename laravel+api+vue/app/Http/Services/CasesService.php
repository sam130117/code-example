<?php

namespace App\Http\Services;


use App\Models\Cases;

class CasesService extends BaseService
{
    const MODEL_NAME = Cases::class;

    public function getAll()
    {
        $search = request()->get('search', null);
        $query = (self::MODEL_NAME)::select('id', 'title');
        if ($search)
            $query->where('title', 'LIKE', "%$search%");
        return $query->paginate();
    }

    public function getCaseWithCards($id)
    {
        return (self::MODEL_NAME)::with(['cards', 'user'])
            ->where('id', $id)
            ->first();
    }

    public function updateByIdWithCards($id, array $caseData, ?array $cardsData)
    {
        $case = (self::MODEL_NAME)::with('cards')->find($id);
        $case->fill($caseData);
        $case->save();

        if ($cardsData)
            foreach ($case->cards as $index => $card)
                $card->fill($cardsData[$index])->save($cardsData[$index]);

        return $case;
    }
}
