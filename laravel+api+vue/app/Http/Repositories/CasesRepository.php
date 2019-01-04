<?php

namespace App\Http\Repositories;


use App\Models\BaseModel;
use App\Models\Cases;
use Illuminate\Pagination\LengthAwarePaginator;

class CasesRepository extends BaseRepository
{
    function model(): string
    {
        return Cases::class;
    }
    
    public function getAll(): LengthAwarePaginator
    {
        $search = request()->get('search', null);
        $cases = $this->model::select('id', 'title');
        if ($search)
            $cases->where('title', 'LIKE', "%$search%");
        return $cases->paginate();
    }

    public function getCaseWithCards($id): BaseModel
    {
        return $this->model::with(['cards', 'user'])
            ->where('id', $id)
            ->first();
    }

    public function updateByIdWithCards($id, array $caseData, ?array $cardsData): BaseModel
    {
        $case = $this->model::with('cards')->find($id);
        $case->fill($caseData);
        $case->save();

        if ($cardsData)
            foreach ($case->cards as $index => $card)
                $card->fill($cardsData[$index])->save($cardsData[$index]);

        return $case;
    }
}
