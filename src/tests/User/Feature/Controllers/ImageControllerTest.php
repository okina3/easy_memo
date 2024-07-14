<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Image;
use App\Models\User;
use App\Services\ImageService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\User\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * 画像を作成するヘルパーメソッド
     * @param int $count 画像の作成数
     * @return Collection 作成された画像のコレクション
     */
    private function createImages(int $count): Collection
    {
        // 指定された数の画像を、現在のユーザーに関連付けて作成する
        return Image::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    /**
     * constructメソッドが正しく動作することをテスト
     * @return void
     */
    public function testConstructImageController()
    {
        // 1件の別のユーザーを作成
        $anotherUser = User::factory()->create();
        // 1件の別のユーザーの画像を作成
        $anotherUserImage = Image::factory()->create(['user_id' => $anotherUser->id]);

        // constructメソッドが正しく動作して、別のユーザーの画像にアクセスできないことを確認
        $response = $this->get(route('user.image.show', $anotherUserImage->id));
        $response->assertStatus(404);
    }

    /**
     * 画像の一覧が、正しく表示されることをテスト
     * @return void
     */
    public function testIndexImageController()
    {
        // 5件の画像を作成
        $images = $this->createImages(2);

        // 画像の一覧を表示する為に、リクエストを送信
        $response = $this->get(route('user.image.index'));

        // レスポンスが 'user.images.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.images.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_images', function ($viewImages) use ($images) {
            // ビューから取得したメモをコレクションに変換
            $viewImages = collect($viewImages);
            // dataキーの中の画像を、配列で取得し、コレクションに変換
            $viewImagesData = collect($viewImages->get('data', []));
            // ビューに渡される画像が、2件であり、かつ、画像のID配列も、一致することを確認
            return $viewImagesData->count() === 2 &&
                $viewImagesData->pluck('id')->toArray() === $images->pluck('id')->toArray();
        });
    }

    /**
     * 画像の新規登録画面が、正しく表示されることをテスト
     * @return void
     */
    public function testCreateImageController()
    {
        // 画像の新規作成画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.image.create'));

        // レスポンスが 'user.images.create' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.images.create');
    }

    /**
     * 画像が、正しく保存されることをテスト
     * @return void
     */
    public function testStoreImageController()
    {
        // ストレージのフェイク
        Storage::fake('public');

        // テスト用のアップロードファイルを作成
        $file = UploadedFile::fake()->image('test_image.jpg');

        // ImageServiceを部分モックして、afterResizingImageメソッドをキャプチャ
        $imageServiceMock = $this->partialMock(ImageService::class, function ($mock) {
            // プロテクトされたメソッドのモックを許可（protectedメソッドをモック対象にできる）
            $mock->shouldAllowMockingProtectedMethods()
                // オブジェクトを部分的にモック化し、元のメソッドの呼び出しを許可
                // オブジェクトの一部のメソッドをモックしないでそのまま使う
                ->makePartial()
                // afterResizingImageメソッドの呼び出しをモック化
                ->shouldReceive('afterResizingImage')
                // モックメソッドが呼ばれたときに実行するコールバックを設定
                ->andReturnUsing(function ($image_file, $manager) {
                    // 実際のafterResizingImageメソッドを呼び出し、その戻り値をキャプチャし、テストで使用
                    return ImageService::afterResizingImage($image_file, $manager);
                });
        });
        // サービスコンテナにモックをバインド
        // テスト対象のコントローラやサービスがImageServiceを使用する際に、
        // モックオブジェクトが注入され、実際の実装の代わりにモックの振る舞いを利用することができる
        $this->app->instance(ImageService::class, $imageServiceMock);

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // 画像を保存するの為に、リクエストを送信
        $response = $this->post(route('user.image.store'), ['images' => $file]);

        // レスポンスが 'image.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.image.index'));
        $response->assertSessionHas(['message' => '画像を登録しました。', 'status' => 'info']);

        // 画像が保存されたかを検証するために、最新のImageレコードを取得
        $latestImage = Image::latest()->first();
        $capturedFileName = $latestImage->filename;

        // データベースにファイル名が保存されていることを確認
        $this->assertDatabaseHas('images', [
            'filename' => $capturedFileName,
            'user_id' => $this->user->id,
        ]);

        // ストレージにファイルが保存されていることを確認
        Storage::disk('public')->assertExists($capturedFileName);
    }

    /**
     * 画像が、正しく保存される時のエラーハンドリングをテスト
     * @return void
     */
    public function testErrorStoreImageController()
    {
        // ストレージのフェイク
        Storage::fake('public');

        // テスト用のアップロードファイルを作成
        $file = UploadedFile::fake()->image('test_image.jpg');

        // ImageServiceを部分モックして、afterResizingImageメソッドをキャプチャ
        $imageServiceMock = $this->partialMock(ImageService::class, function ($mock) {
            // プロテクトされたメソッドのモックを許可（protectedメソッドをモック対象にできる）
            $mock->shouldAllowMockingProtectedMethods()
                // オブジェクトを部分的にモック化し、元のメソッドの呼び出しを許可
                // オブジェクトの一部のメソッドをモックしないでそのまま使う
                ->makePartial()
                // afterResizingImageメソッドの呼び出しをモック化
                ->shouldReceive('afterResizingImage')
                // モックメソッドが呼ばれたときに実行するコールバックを設定
                ->andReturnUsing(function ($image_file, $manager) {
                    // 実際のafterResizingImageメソッドを呼び出し、その戻り値をキャプチャし、テストで使用
                    return ImageService::afterResizingImage($image_file, $manager);
                });
        });
        // サービスコンテナにモックをバインド
        // テスト対象のコントローラやサービスがImageServiceを使用する際に、
        // モックオブジェクトが注入され、実際の実装の代わりにモックの振る舞いを利用することができる
        $this->app->instance(ImageService::class, $imageServiceMock);

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // DB::transactionメソッドが呼び出されると、一度だけ例外をスローするように設定
        DB::shouldReceive('transaction')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->with(Mockery::type(Exception::class));

        // 例外がスローされることを期待し、そのメッセージが"DBエラー"であることを確認
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('DBエラー');

        // 画像を保存するリクエストを送信
        $this->post(route('user.image.store'), ['images' => $file]);
    }

    /**
     * 画像の詳細が、正しく表示されることをテスト
     * @return void
     */
    public function testShowImageController()
    {
        // 1件の画像を作成
        $image = $this->createImages(1)->first();

        // 画像詳細画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.image.show', $image->id));

        // レスポンスが 'user.images.show' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.images.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_image', function ($viewImage) use ($image) {
            // ビューに渡される画像のIDが、作成した画像のIDと一致することを確認
            return $viewImage->id === $image->id;
        });
    }

    /**
     * 画像が、正しく削除されることをテスト
     * @return void
     */
    public function testDestroyImageController()
    {
        // 1件の画像を作成
        $image = $this->createImages(1)->first();

        // 画像を削除する為に、リクエストを送信
        $response = $this->delete(route('user.image.destroy', ['imageId' => $image->id]));

        // 画像が削除されたことを確認
        $this->assertDatabaseMissing('images', ['id' => $image->id]);

        // レスポンスが 'image.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.image.index'));
        $response->assertSessionHas(['message' => '画像を削除しました。', 'status' => 'alert']);
    }

    /**
     * 画像が、正しく削除される時のエラーハンドリングをテスト
     * @return void
     */
    public function testErrorDestroyImageController()
    {
        // 1件の画像を作成
        $image = $this->createImages(1)->first();

        // DB::transactionメソッドが呼び出されると、一度だけ例外をスローするように設定
        DB::shouldReceive('transaction')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->with(Mockery::type(Exception::class));

        // 例外がスローされることを期待し、そのメッセージが"DBエラー"であることを確認
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('DBエラー');

        // 画像を削除する為に、リクエストを送信
        $this->delete(route('user.image.destroy', ['imageId' => $image->id]));
    }
}
