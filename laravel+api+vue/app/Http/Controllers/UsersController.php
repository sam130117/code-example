<?php

namespace App\Http\Controllers;

use App\Http\Services\UsersService;

class UsersController extends Controller
{
    protected $usersService;
    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function index()
    {
        return view('index');
    }
}
