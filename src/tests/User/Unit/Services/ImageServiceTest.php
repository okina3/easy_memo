<?php

namespace Tests\User\Unit\Services;

use App\Models\Image;
use App\Models\Memo;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class ImageServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     */
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 2人目の別のユーザーを作成
        $this->secondaryUser = User::factory()->create();

        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * メモを作成するヘルパーメソッド
     * @param int $count メモの作成数
     * @return Collection 作成されたメモのコレクション
     */
    private function createMemos(int $count): Collection
    {
        // 指定された数のメモを、現在のユーザーに関連付けて作成する
        return Memo::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    /**
     * メモに画像を関連付けるヘルパーメソッド
     * @param Memo $memo 関連付けるメモ
     * @param int $imageCount 作成する画像の数
     * @return Collection Collection 作成された画像のコレクション
     */
    private function attachImages(Memo $memo, int $imageCount): Collection
    {
        // 画像を作成し、メモに関連付け
        $images = Image::factory()->count($imageCount)->create();
        $memo->images()->attach($images);

        // 画像を返す
        return $images;
    }

    /**
     * 別のユーザーの画像を見られなくするメソッドのテスト
     *
     * @return void
     */
    public function testCheckUserImage()
    {
        // 2人目のユーザーを作成
        $secondaryUser = User::factory()->create();
        // 2人目のユーザーの画像を作成
        $secondaryUserMemo = Image::factory()->create(['user_id' => $secondaryUser->id]);

        // リクエストを作成
        $request = Request::create('/images/' . $secondaryUserMemo->id);
        // リクエストのルートを設定
        $request->setRouteResolver(function () use ($request) {
            // 新しいルートオブジェクトを作成し、リクエストにバインド
            return (new Route('GET', '/images/{image}', []))->bind($request);
        });

        // 異常なユーザーの画像アクセスは、例外の発生を期待（404エラー）
        $this->expectException(NotFoundHttpException::class);
        // 画像の所有者を確認するサービスメソッドを実行
        ImageService::checkUserImage($request);
    }

    /**
     * 選択したメモに紐づいた画像を取得するテスト
     *
     * @return void
     */
    public function testGetMemoImages()
    {
        // 1件の自分のメモを作成
        $memo = $this->createMemos(1)->first();
        // メモに2件のタグを関連付け
        $attachImages = $this->attachImages($memo, 2);
        // 選択したメモに紐づいた画像を取得するサービスメソッドを実行
        $memo_images = ImageService::getMemoImages($attachImages);

        // サービスメソッドから取得した情報をコレクションに変換
        $memo_images = collect($memo_images);
        // 作成した関連付けられた画像のID配列が、取得したメモに紐づいた画像のID配列と、一致しているかを確認
        $this->assertEquals($attachImages->pluck('id')->toArray(), $memo_images->pluck('id')->toArray());
    }

    /**
     * 選択したメモに紐づいた画像のidを取得するテスト
     *
     * @return void
     */
    public function testGetMemoImagesId()
    {
        // 1件の自分のメモを作成
        $memo = $this->createMemos(1)->first();
        // メモに2件のタグを関連付け
        $attachImages = $this->attachImages($memo, 2);
        // 選択したメモに紐づいた画像を取得するサービスメソッドを実行
        $memo_images = ImageService::getMemoImagesId($attachImages);

        // 作成した関連付けられた画像のID配列が、取得したメモに紐づいた画像のID配列と、一致しているかを確認
        $this->assertEquals($attachImages->pluck('id')->toArray(), $memo_images);
    }

    /**
     * 画像をリサイズして、Laravelのフォルダ内に保存するテスト
     *
     * @return void
     */
    public function testAfterResizingImage()
    {
        // ストレージをモックし、ファイル操作をシミュレーション
        Storage::fake('public');

        // テスト用の画像ファイルをアップロード
        $uploadedFile = UploadedFile::fake()->image('test_image.jpg');
        // ImageManagerの初期化
        $manager = new ImageManager(new Driver());
        // 画像リサイズ後のファイル名を取得するサービスメソッドを実行
        $fileName = ImageService::afterResizingImage($uploadedFile, $manager);

        // リサイズ後の画像がストレージに存在することを確認
        Storage::disk('public')->assertExists($fileName);
    }

    /**
     * Storageフォルダ内の画像ファイルを削除するテスト
     *
     * @return void
     */
    public function testDeleteStorage()
    {
        // ダミー画像のファイル名を定義
        $imageFilename = 'test_image.jpg';
        // ダミー画像のファイルのパスを定義
        $filePath = 'public/' . $imageFilename;
        // ストレージに、ファイル名を内容とするダミー画像を作成
        Storage::put($filePath, $imageFilename);
        // ストレージにファイルが存在することを確認
        $this->assertTrue(Storage::exists($filePath));

        // ストレージの画像ファイルを削除するサービスメソッドを実行
        ImageService::deleteStorage($imageFilename);

        // 画像ファイルが削除されたことを確認
        $this->assertFalse(Storage::exists($filePath));
    }
}
