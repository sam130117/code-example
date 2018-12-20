<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UsersApiRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\Http\Services\UsersService;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    protected $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function index(): ResourceCollection
    {
        return new UsersResource($this->usersService->getAll());
    }

    public function store(UsersApiRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password']);
        User::create($data);

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function show($id): JsonResource
    {
        return new UserResource($this->usersService->getById($id));
    }

    public function update(UsersApiRequest $request, $id): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password']);
        $this->usersService->updateById($id, $data);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy($id): JsonResponse
    {
        $this->usersService->deleteById($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
