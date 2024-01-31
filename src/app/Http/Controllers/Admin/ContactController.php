<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // 全ての問い合わせ情報を取得する
        $contact_all = Contact::with('user')
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('admin.contacts.index', compact('contact_all'));
    }


    public function show(int $id)
    {
        // // 選択した問い合わせ情報を取得する
        // $choice_contact = Contact::with('user')
        // ->where('id', $id)
        // ->orderBy('updated_at', 'desc')
        // ->first();

        // dd($choice_contact);

        // return view('admin.contacts.show', compact('choice_contact'));
        return view('admin.contacts.show');
    }


    public function destroy()
    {
        return to_route('admin.contact.index');
    }
}
