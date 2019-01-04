<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CasesApiRequest;
use App\Http\Resources\CaseResource;
use App\Http\Resources\CasesResource;
use App\Http\Repositories\CasesRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class CasesController extends Controller
{
    protected $casesRepository;
    public function __construct(CasesRepository $casesRepository)
    {
        $this->casesRepository = $casesRepository;
    }

    public function index(): ResourceCollection
    {
        return new CasesResource($this->casesRepository->getAll());
    }

    public function store(CasesApiRequest $request): JsonResponse
    {
        $data = $request->only(['title', 'client_email', 'website', 'country', 'user_id']);
        $this->casesRepository->create($data);

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function show($id): JsonResource
    {
        return new CaseResource($this->casesRepository->getById($id));
    }

    public function update(CasesApiRequest $request, $id): JsonResponse
    {
        $data = $request->only(['title', 'client_email', 'website', 'country', 'user_id']);
        $this->casesRepository->updateById($id, $data);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy($id): JsonResponse
    {
        $this->casesRepository->deleteById($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
