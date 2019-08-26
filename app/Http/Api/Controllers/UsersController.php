<?php

namespace App\Http\Api\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;

class UsersController extends Controller
{
    use Helpers;

    public function __construct()
    {
        $this->middleware('api.auth');
    }

    public function index()
    {
//        return User::all();
        $user = $this->auth->user();

        return $user;
    }
}