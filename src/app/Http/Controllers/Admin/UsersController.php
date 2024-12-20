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
        $all_users = User::availableAllUsers()->searchKeyword($request->keyword)->get();

        return view('admin.users.index', compact('all_users'));
    }

    /**
     * ユーザーのサービス利用を停止（ソフトデリート）するメソッド。
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // 自分の全てのメモの共有設定を解除する
                UserService::deleteUserShareSettingAll($request->userId);
                // 選択したユーザーのサービス利用を停止
                User::findOrFail($request->userId)->delete();
            }, 10);

            return to_route('admin.index')->with(['message' => 'ユーザーのサービス利用を停止しました', 'status' => 'alert']);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
    }
}
