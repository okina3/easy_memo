<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
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
}
