<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrashedContactController extends Controller
{
    /**
     * ソフトデリートした問い合わせ一覧を表示するメソッド。
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // 警告したユーザーを取得する
        $trashed_contacts = Contact::onlyTrashed()
        // ->availableAllUsers()
        // ->searchKeyword($request->keyword)
        ->get();
        // dd($trashed_contacts);

        return view('admin.trashedContacts.index');
        // return view('admin.trashedContacts.index', compact('trashed_contacts'));
    }

    /**
     * ソフトデリートした問い合わせを元に戻すメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function undo(Request $request): RedirectResponse
    {
        Contact::onlyTrashed()->availableSelectUser($request->userId)->restore();

        return to_route('admin.trashed-contact.index')
        ->with(['message' => 'ユーザーの問い合わせを、元に戻しました。', 'status' => 'info']);
    }

    /**
     * ソフトデリートした問い合わせをを完全削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Contact::onlyTrashed()->availableSelectUser($request->userId)->forceDelete();

        return to_route('admin.trashed-contact.index')
        ->with(['message' => 'ユーザーの問い合わせを、完全に削除しました。', 'status' => 'alert']);
    }
}
