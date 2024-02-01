<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * ユーザーの問い合わせ一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // 全ての問い合わせ情報を取得する
        $contact_all = Contact::with('user')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.contacts.index', compact('contact_all'));
    }

    /**
     * ユーザーの問い合わせの詳細を表示するメソッド。
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        // 選択した問い合わせ情報を取得する
        $choice_contact = Contact::with('user')
            ->where('id', $id)
            ->orderBy('updated_at', 'desc')
            ->first();

        return view('admin.contacts.show', compact('choice_contact'));
    }

    /**
     * ユーザーの問い合わせを削除（ソフトデリート）するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 選択した問い合わせ情報を削除する
        Contact::where('id', $request->contentId)->delete();

        return to_route('admin.contact.index')->with(['message' => 'ユーザーの問い合わせをゴミ箱に移動しました。', 'status' => 'alert']);
    }
}
