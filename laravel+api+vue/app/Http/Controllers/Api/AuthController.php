<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UsersApiRequest;
use App\Http\Repositories\UsersRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
            $user = Auth::user();
            dd($user->token(), $user->tokens()->get()->toArray());
            $tokenResult = $this->usersRepository->saveUserToken($user);

            return response()->json([
                'code'         => Response::HTTP_OK,
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            ], Response::HTTP_OK);
        } else
            return response()->json([
                'code'  => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized.',
            ], Response::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json([
            'code'    => Response::HTTP_OK,
            'message' => 'Successfully logged out.',
        ], Response::HTTP_OK);
    }

    public function signUp(UsersApiRequest $request): JsonResponse
    {
        $data = $request->only(['name', 'email', 'password', 'password_confirm']);
        $user = $this->usersRepository->create($data);
        $tokenResult = $this->usersRepository->saveUserToken($user);

        return response()->json([
            'code'         => Response::HTTP_OK,
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ], Response::HTTP_OK);
    }

}
