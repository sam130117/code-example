<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Repositories\UsersRepository;


class UsersController extends Controller
{
    protected $usersService;

    public function __construct(UsersRepository $usersService)
    {
        $this->usersService = $usersService;
    }

    public function index()
    {
        return view('index');
    }
}
