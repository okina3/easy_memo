<?php

namespace Tests\User\Unit\Services;

use App\Models\Image;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class ImageServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Image $image;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     */
    protected function setUp(): void
    {
        parent::setUp();
        // テスト用のユーザー、画像を生成
        $this->user = User::factory()->create();
        $this->image = Image::factory()->create(['user_id' => $this->user->id]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);
    }

    /**
     * checkUserImageメソッドのテスト。
     *
     * @return void
     */
    public function testCheckUserImage()
    {
        // 特定の画像IDを持つリクエストを作成
        $request = Request::create('/images/' . $this->image->id);
        $request->setRouteResolver(function () use ($request) {
            return (new Route('GET', '/images/{image}', []))->bind($request, ['image' => $this->image->id]);
        });

        // 正常なユーザーの場合、例外は発生しない
        ImageService::checkUserImage($request);

        // 異常なユーザーの場合、404エラーが発生することを確認
        $anotherUser = User::factory()->create();
        $anotherImage = Image::factory()->create(['user_id' => $anotherUser->id]);

        // 特定の画像IDを持つリクエストを作成（異常なユーザーの場合）
        $request = Request::create('/images/' . $anotherImage->id);
        $request->setRouteResolver(function () use ($request, $anotherImage) {
            return (new Route('GET', '/images/{image}', []))->bind($request, ['image' => $anotherImage->id]);
        });

        // 異常なユーザーの画像アクセスは404エラーを発生させる
        $this->expectException(NotFoundHttpException::class);
        ImageService::checkUserImage($request);
    }

    /**
     * getMemoImagesメソッドのテスト。
     *
     * @return void
     */
    public function testGetMemoImages()
    {
        $images = new Collection([$this->image]);
        $result = ImageService::getMemoImages($images);

        // 結果が1件であり、画像IDが正しいことを確認
        $this->assertCount(1, $result);
        $this->assertEquals($this->image->id, $result[0]->id);
    }

    /**
     * getMemoImagesIdメソッドのテスト。
     *
     * @return void
     */
    public function testGetMemoImagesId()
    {
        $images = new Collection([$this->image]);
        $result = ImageService::getMemoImagesId($images);

        // 結果が1件であり、画像IDが正しいことを確認
        $this->assertCount(1, $result);
        $this->assertEquals($this->image->id, $result[0]);
    }

    /**
     * afterResizingImageメソッドのテスト。
     *
     * @return void
     */
    public function testAfterResizingImage()
    {
        Storage::fake('public');

        $uploadedFile = UploadedFile::fake()->image('test_image.jpg');

        // ImageManagerの初期化
        $manager = new ImageManager(new Driver());

        // リサイズ後の画像ファイル名を取得
        $fileName = ImageService::afterResizingImage($uploadedFile, $manager);

        // ファイル名がjpegで終わっていることを確認
        $this->assertStringEndsWith('.jpeg', $fileName);
    }

    /**
     * deleteStorageメソッドのテスト。
     *
     * @return void
     */
    public function testDeleteStorage()
    {
        // ストレージにダミー画像ファイルを作成
        $imageFilename = 'test_image.jpg';
        $filePath = 'public/' . $imageFilename;
        Storage::put($filePath, 'dummy content');

        // 画像ファイルが存在することを確認
        $this->assertTrue(Storage::exists($filePath));

        // deleteStorageメソッドを実行
        ImageService::deleteStorage($imageFilename);

        // 画像ファイルが削除されたことを確認
        $this->assertFalse(Storage::exists($filePath));
    }
}
