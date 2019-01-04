<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\CaseWithCardsRequest;
use App\Http\Repositories\CasesRepository;
use App\Http\Repositories\UsersRepository;
use App\Http\Services\CasesService;
use App\Models\Cases;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;


class CasesController extends Controller
{
    protected $casesService;
    protected $casesRepository;
    protected $usersRepository;

    public function __construct(CasesService $casesService, CasesRepository $casesRepository, UsersRepository $usersRepository)
    {
        $this->casesService = $casesService;
        $this->casesRepository = $casesRepository;
        $this->usersRepository = $usersRepository;
    }

    public function index()
    {
        return view('index');
    }

    public function getCases(): JsonResponse
    {
        $cases = $this->casesRepository->getAll();
        return response()->json(['cases' => $cases], Response::HTTP_OK);
    }

    public function getCase($id): JsonResponse
    {
        $data = $this->casesService->getCaseWithUsers($id);
        return response()->json($data);
    }

    public function store(CaseWithCardsRequest $request): JsonResponse
    {
        $caseId = $request->get('id');
        $caseData = $request->only(['title', 'client_email', 'website', 'country', 'user_id']);
        $cardsData = $request->only('cards');
        $this->casesRepository->updateByIdWithCards($caseId, $caseData, $cardsData['cards'] ?? null);

        return response()->json(null, Response::HTTP_OK);
    }

    public function destroy(Cases $case): JsonResponse
    {
        $this->casesRepository->deleteById($case->id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

}
