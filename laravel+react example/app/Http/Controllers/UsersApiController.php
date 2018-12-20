<?php

namespace App\Http\Controllers\Api;

use App\Models\LogUsersActions;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class UsersApiController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->select('*')->get();
        return response()->json(['users' => $users, 'authUserId' => Auth::user()->id], Response::HTTP_OK);
    }

    public function updateRights(Request $request, User $user)
    {
        $user->extended_rights = !$user->extended_rights;
        $user->save();
        LogUsersActions::saveAction("User <b>" . $request->name . "</b> was successfully updated.");

        return response()->json(null, Response::HTTP_OK);
    }

    public function delete(Request $request, User $user)
    {
        try {
            $user->delete();
            LogUsersActions::saveAction("User <b>" . $user->name . "</b> was successfully deleted.");
            return response()->json(null, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }


}