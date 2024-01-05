<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Image;
use App\Services\ImageService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

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

    /**
     * 画像を保存するメソッド。
     * @param UploadImageRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(UploadImageRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // 選択された画像を取得
                $image_files = $request->file();
                // もし画像が選択されている場合はリサイズ
                if (!is_null($image_files)) {
                    foreach ($image_files as $image_file) {
                        // 画像をリサイズして、Laravelのフォルダ内に保存
                        $only_one_file_name = ImageService::afterResizingImage($image_file);
                        // リサイズした画像をDBに保存
                        Image::create([
                            'user_id' => Auth::id(),
                            'filename' => $only_one_file_name
                        ]);
                    }
                }
            }, 10);
        } catch (Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return to_route('image.index')->with(['message' => '画像を登録しました。', 'status' => 'info']);
    }
}
