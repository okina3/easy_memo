<?php

namespace Tests\User\Feature;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;
use Illuminate\Support\Facades\Log;

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
     * 画像の一覧が正しく表示されることをテスト
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
            // ビューに渡される画像が、2件であり、かつ、作成した画像のID配列と一致することを確認
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
