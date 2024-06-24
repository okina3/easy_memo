<?php

namespace Tests\User\Feature;

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class ImageControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    public function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ログインユーザーを作成し、プロパティに格納
        $this->user = $this->createUserWithAuthenticatedSession();
    }

    /**
     * ログインユーザーを作成し認証済みセッションを開始するヘルパーメソッド
     * @return User 認証済みのユーザーオブジェクト
     */
    public function createUserWithAuthenticatedSession(): User
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // ユーザーを認証
        $this->actingAs($user);
        // 認証済みのユーザーを返す
        return $user;
    }

    /**
     * constructメソッドが正しく動作することをテスト
     * @return void
     */
    public function testConstructImageController()
    {
        // 別のユーザーを作成
        $anotherUser = User::factory()->create();
        // 別のユーザーの画像を作成
        $anotherUserImage = Image::factory()->create(['user_id' => $anotherUser->id]);

        // constructメソッドが正しく動作して、別のユーザーの画像にアクセスできないことを確認
        $response = $this->get(route('user.image.show', $anotherUserImage->id));
        $response->assertStatus(404);
    }

    /**
     * 画像の一覧が正しく表示されることをテスト
     * @return void
     */
    public function testIndexImageController()
    {
        // 画像を作成
        $images = Image::factory()->count(5)->create(['user_id' => $this->user->id]);

        // indexメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.image.index'));

        // レスポンスが 'user.images.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.images.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_images', function ($viewImages) use ($images) {
            return $viewImages->count() === 5 && $viewImages->first()->user_id === $images->first()->user_id;
        });
    }

    /**
     * 画像の新規登録画面が、正しく表示されることをテスト
     * @return void
     */
    public function testCreateImageController()
    {
        // createメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.image.create'));

        // レスポンスが 'user.images.create' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.images.create');
    }


    /**
     * 画像の詳細が正しく表示されることをテスト
     * @return void
     */
    public function testShowImageController()
    {
        // 画像を作成
        $image = Image::factory()->create(['user_id' => $this->user->id]);

        // showメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.image.show', $image->id));

        // レスポンスが 'user.images.show' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.images.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_image', function ($viewImage) use ($image) {
            return $viewImage->id === $image->id && $viewImage->user_id === $image->user_id;
        });
    }
}
