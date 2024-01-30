<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();

        return to_route('user.contact.create');
    }


    public function create()
    {
        return view('user.contacts.create');
    }

    public function store(Request $request)
    {

        return ;
    }
}
