<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * 管理人への問い合わせの新規作成画面を表示するメソッド。
     * @return View
     */
    public function create(): View
    {
        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();

        return view('user.contacts.create');
    }

    /**
     * 管理人への問い合わせを、保存するメソッド。
     * @param ContactRequest $request
     * @return RedirectResponse
     */
    public function store(ContactRequest $request): RedirectResponse
    {
        // ブラウザバック対策（値を確認）
        SessionService::clickBrowserBackSession();
        // 問い合わせ情報を保存
        Contact::availableCreateContact($request);

        return to_route('user.index')->with(['message' => '管理人にメッセージを送りました。', 'status' => 'info']);
    }
}
