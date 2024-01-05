<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\ImageService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImageController extends Controller
{
    public function __construct()
    {
        //別のユーザーの画像を見られなくする認証。
        $this->middleware(function (Request $request, Closure $next) {
            ImageService::imageUserCheck($request);
            return $next($request);
        });
    }

    /**
     * 画像の一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // 全画像を取得する
        $images = Image::availableImageAll()->paginate(20);

        return view('user.images.index', compact('images'));
    }

    /**
     * 画像の新規登録画面を表示するメソッド。
     * @return View
     */
    public function create(): View
    {
        return view('user.images.create');
    }

}
