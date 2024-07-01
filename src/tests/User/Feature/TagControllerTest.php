<?php

namespace Tests\User\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * タグを作成するヘルパーメソッド
     * @param int $count タグの作成数
     * @return Collection 作成されたタグのコレクション
     */
    private function createTags(int $count): Collection
    {
        // 指定された数のタグを、現在のユーザーに関連付けて作成する
        return Tag::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    /**
     * タグの一覧が正しく表示されることをテスト
     * @return void
     */
    public function testIndexTagController()
    {
        // 3件のタグを作成
        $tags = $this->createTags(3);

        // タグの一覧を表示する為に、リクエストを送信
        $response = $this->get(route('user.tag.index'));

        // レスポンスが 'user.tags.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.tags.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_tags', function ($viewTags) use ($tags) {
            // ビューから取得したタグをコレクションに変換
            $viewTags = collect($viewTags);
            // ビューに渡されるタグが、3件であり、かつ、作成したタグのID配列と一致することを確認
            return $viewTags->count() === 3 && $viewTags->pluck('id')->toArray() === $tags->pluck('id')->toArray();
        });
    }

    /**
     * タグが正しく保存されることをテスト
     * @return void
     */
    public function testStoreTagController()
    {
        // 1件の新規タグを作成
        $newTag = 'テスト、新規タグ';

        // 保存するデータを作成
        $requestData = ['new_tag' => $newTag,];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // タグ保存のリクエストを送信
        $response = $this->post(route('user.tag.store'), $requestData);

        // タグが保存されたことを確認
        $this->assertDatabaseHas('tags', ['name' => 'テスト、新規タグ', 'user_id' => $this->user->id]);

        // レスポンスが 'tag.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => 'タグを登録しました。', 'status' => 'info']);
    }

    /**
     * タグが正しく削除（複数）されることをテスト
     * @return void
     */
    public function testDestroyTagController()
    {
        // 3件のタグを作成
        $tags = $this->createTags(3);

        // 作成したタグのIDを、配列として取得
        $tagsId = $tags->pluck('id')->toArray();

        // 削除するタグのID（複数）のデータを作成
        $requestData = ['tags' => $tagsId];

        // タグ削除メソッドを呼び出してレスポンスを確認
        $response = $this->delete(route('user.tag.destroy'), $requestData);

        // タグが削除されたことを確認
        foreach ($tagsId as $tagId) {
            $this->assertDatabaseMissing('tags', ['id' => $tagId]);
        }

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => 'タグを削除しました。', 'status' => 'alert']);
    }
}
