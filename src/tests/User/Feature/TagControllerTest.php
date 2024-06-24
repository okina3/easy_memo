<?php

namespace Tests\User\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\User\TestCase;

class TagControllerTest extends TestCase
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
        // ログインユーザーを作成し、プロパティに格納
        $this->user = $this->createUserWithAuthenticatedSession();
    }

    /**
     * ログインユーザーを作成し認証済みセッションを開始するヘルパーメソッド
     * @return User 認証済みのユーザーオブジェクト
     */
    private function createUserWithAuthenticatedSession(): User
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // ユーザーを認証
        $this->actingAs($user);
        // 認証済みのユーザーを返す
        return $user;
    }

    /**
     * タグの一覧が正しく表示されることをテスト
     * @return void
     */
    public function testIndexTagController()
    {
        // タグを作成
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);

        // indexメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.tag.index'));

        // レスポンスが 'user.tags.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.tags.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_tags', function ($viewTags) use ($tags) {
            return $viewTags->count() === 3 && $viewTags->first()->user_id === $tags->first()->user_id;
        });
    }

    /**
     * タグが正しく保存されることをテスト
     * @return void
     */
    public function testStoreTagController()
    {
        // 新規タグを作成
        $requestData = ['new_tag' => 'テストタグ'];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // タグ保存メソッドを呼び出してレスポンスを確認
        $response = $this->post(route('user.tag.store'), $requestData);

        // タグが保存されたことを確認
        $this->assertDatabaseHas('tags', [
            'name' => 'テストタグ',
            'user_id' => $this->user->id,
        ]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => 'タグを登録しました。', 'status' => 'info']);
    }

    /**
     * タグが正しく削除されることをテスト
     * @return void
     */
    public function testDestroyTagController()
    {
        // タグを作成
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);

        // 作成したタグのIDを配列として取得
        $tagIds = $tags->pluck('id')->toArray();

        // リクエストデータを作成
        $requestData = ['tags' => $tagIds];

        // タグ削除メソッドを呼び出してレスポンスを確認
        $response = $this->delete(route('user.tag.destroy'), $requestData);

        // タグが削除されたことを確認
        foreach ($tagIds as $tagId) {
            $this->assertDatabaseMissing('tags', ['id' => $tagId]);
        }

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => 'タグを削除しました。', 'status' => 'alert']);
    }
}
