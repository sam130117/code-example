<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CasesApiRequest;
use App\Http\Resources\CaseResource;
use App\Http\Resources\CasesResource;
use App\Http\Services\CasesService;
use App\Models\Cases;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class CasesController extends Controller
{
    protected $casesService;
    public function __construct(CasesService $casesService)
    {
        $this->casesService = $casesService;
    }

    public function index(): ResourceCollection
    {
        return new CasesResource($this->casesService->getAll());
    }

    public function store(CasesApiRequest $request): JsonResponse
    {
        $data = $request->only(['title', 'client_email', 'website', 'country', 'user_id']);
        Cases::create($data);

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function show($id): JsonResource
    {
        return new CaseResource($this->casesService->getById($id));
    }

    public function update(CasesApiRequest $request, $id): JsonResponse
    {
        $data = $request->only(['title', 'client_email', 'website', 'country', 'user_id']);
        $this->casesService->updateById($id, $data);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy($id): JsonResponse
    {
        $this->casesService->deleteById($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
