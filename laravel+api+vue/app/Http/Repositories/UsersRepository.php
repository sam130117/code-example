<?php

namespace App\Http\Repositories;

use App\Models\BaseModel;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\PersonalAccessTokenResult;


class UsersRepository extends BaseRepository
{
    function model(): string
    {
        return User::class;
    }

    public function getAll(): BaseModel
    {
        return $this->model::get();
    }

    public function saveUserToken(User $user): PersonalAccessTokenResult
    {
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if (request()->get('remember_me', null))
            $token->expires_at = Carbon::now()->addWeeks(1);
        else
            $token->expires_at = Carbon::now()->addHours(1);
        $token->save();
        return $tokenResult;
    }

    public function getAllIds(): array
    {
        return $this->model::select('id')->get()->pluck('id')->toArray();
    }

    public function getRandomUser(): BaseModel
    {
        $userIds = $this->getAllIds();
        $randomUserId = array_rand($userIds,1);

        return $this->model::where('id', $randomUserId)->first();
    }

}
