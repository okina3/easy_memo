<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * タグの一覧を表示するメソッド。
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // タグを取得する
        $all_tags = Tag::availableTagAll()->get();

        return view('user.tags.index', compact('all_tags'));
    }

    /**
     * タグを保存するメソッド。
     * @param UploadTagRequest $request
     * @return RedirectResponse
     */
    public function store(UploadTagRequest $request): RedirectResponse
    {
        // タグが重複していないか調べる
        $tag_exists = Tag::availableTagExists($request)->exists();
        // タグが、重複していなれば、タグを保存
        if (!empty($request->new_tag) && !$tag_exists) {
            Tag::create([
                'name' => $request->new_tag,
                'user_id' => Auth::id()
            ]);
        }
        return to_route('user.tag.index')->with(['message' => 'タグを登録しました。', 'status' => 'info']);
    }

    /**
     * タグを削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        //タグを複数まとめて削除
        foreach ($request->tags as $tag) {
            Tag::findOrFail($tag)->delete();
        }
        return to_route('user.tag.index')->with(['message' => 'タグを削除しました。', 'status' => 'alert']);
    }
}
