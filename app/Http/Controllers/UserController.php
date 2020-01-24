<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $model=User::with('role')->paginate(25);

        return $model;
    }

    public function list_user(Request $request)
    {
        return User::all();
    }
}