<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UsersApiRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\Http\Repositories\UsersRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    protected $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function index(): ResourceCollection
    {
        return new UsersResource($this->usersRepository->getAll());
    }

    public function store(UsersApiRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password']);
        $this->usersRepository->create($data);

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function show($id): JsonResource
    {
        return new UserResource($this->usersRepository->getById($id));
    }

    public function update(UsersApiRequest $request, $id): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password']);
        $this->usersRepository->updateById($id, $data);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy($id): JsonResponse
    {
        $this->usersRepository->deleteById($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
