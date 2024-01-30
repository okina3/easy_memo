<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    /**
     * @return View
     */
    public function create(): View
    {
        // これいらないか？（後で確かめる）
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();

        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();

        return view('user.contacts.create');
    }

    /**
     * @param ContactRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(ContactRequest $request): RedirectResponse
    {
        // ブラウザバック対策（値を確認）
        SessionService::clickBrowserBackSession();
        try {
            DB::transaction(function () use ($request) {
                // 問い合わせ情報を保存
                Contact::create([
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'user_number' => Auth::id(),
                ]);
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }

        return to_route('user.index')->with(['message' => '管理人にメッセージを送りました。', 'status' => 'info']);
    }
}
