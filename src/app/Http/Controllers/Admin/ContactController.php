<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('admin.contacts.index');
    }


    public function show(int $id)
    {
        return view('admin.contacts.show');
    }


    public function destroy()
    {
        return to_route('admin.contact.index');
    }
}
