<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class UsersController extends Controller
{
    /**
     * 全ユーザー、また、検索したユーザーを表示するメソッド。
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // 全ユーザー、また、検索したユーザーを取得
        $users_all = User::availableUserOrder()
            ->searchKeyword($request->keyword)
            ->paginate(5);

        return view('admin.users.index', compact('users_all'));
    }

    /**
     * ユーザーのサービス利用を停止するメソッド。
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // 自分の全てのメモの共有設定を解除する
                UserService::myShareSettingStop($request->userId);
                // 他のユーザーの、停止ユーザーに共有しているメモの共有を解除する
                UserService::youShareSettingStop($request->userId);
                // 選択したユーザーのサービス利用を停止
                User::findOrFail($request->userId)->delete();
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }

        return to_route('admin.index')->with(['message' => 'ユーザーのサービス利用を停止しました', 'status' => 'alert']);
    }
}
