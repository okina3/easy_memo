<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UsersController extends Controller
{

    public function index()
    {
        // 全ユーザーを取得する
        $users = User::all();

        return view('admin.index', compact('users'));
    }

    public function undo(Request $request)
    {

        
    }

    public function destroy(Request $request)
    {

    }

}
