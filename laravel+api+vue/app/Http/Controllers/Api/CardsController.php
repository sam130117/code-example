<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CardsApiRequest;
use App\Http\Resources\CardResource;
use App\Http\Resources\CardsResource;
use App\Http\Repositories\CardsRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class CardsController extends Controller
{
    protected $cardsRepository;

    public function __construct(CardsRepository $cardsRepository)
    {
        $this->cardsRepository = $cardsRepository;
    }

    public function index(): ResourceCollection
    {
        return new CardsResource($this->cardsRepository->getAll());
    }

    public function store(CardsApiRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'last_number', 'total_value', 'card_type', 'close_date', 'case_id']);
        $this->cardsRepository->create($data);

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function show($id): JsonResource
    {
        return new CardResource($this->cardsRepository->getById($id));
    }

    public function update(CardsApiRequest $request, $id): JsonResponse
    {
        $data = $request->only(['name', 'last_number', 'total_value', 'card_type', 'close_date', 'case_id']);
        $this->cardsRepository->updateById($id, $data);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy($id): JsonResponse
    {
        $this->cardsRepository->deleteById($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
