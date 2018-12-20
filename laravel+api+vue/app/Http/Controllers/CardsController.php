<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardsRequest;
use App\Http\Services\CardsService;
use App\Models\Cards;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CardsController extends Controller
{
    protected $cardsService;

    public function __construct(CardsService $cardsService)
    {
        $this->cardsService = $cardsService;
    }

    public function index()
    {
        return view('index');
    }

    public function getCard(Cards $card): JsonResponse
    {
        return response()->json(['card' => $card, 'cardTypes' => Cards::TYPES]);
    }

    public function getCards(): JsonResponse
    {
        $cards = $this->cardsService->getAll();
        return response()->json(['cards' => $cards]);
    }

    public function destroy(Cards $card): JsonResponse
    {
        $this->cardsService->deleteById($card->id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
