<?php

namespace App\Http\Controllers\Web;

use App\Http\Repositories\UsersRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;


class ChatController extends Controller
{
    protected $userService;

    public function __construct(UsersRepository $userService)
    {
        $this->userService = $userService;
    }

    public function sendMessage(Request $request): JsonResponse
    {
        dd($request->all());
        $redis = Redis::connection();
        $message = $request->get('message', null);
        $user = $this->userService->getRandomUser();

        $data = ['message' => $message, 'user' => $user];
        $redis->publish('messageChannel', json_encode($data));
        return response()->json(null, Response::HTTP_OK);
    }
}
