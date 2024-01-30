<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();

        return to_route('user.contact.create');
    }

    /**
     * @return View
     */
    public function create(): View
    {
        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();

        return view('user.contacts.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {

        return to_route('user.index');
    }
}
