<?php

namespace Tests\User\Feature;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class TrashedMemoControllerTest extends TestCase
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
     * ソフトデリートしたメモ一覧が正しく表示されることをテスト
     * @return void
     */
    public function testIndexTrashedMemoController()
    {
        // ソフトデリートされたメモを作成
        $memos = Memo::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);

        // indexメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.trashed-memo.index'));

        // レスポンスが 'user.trashedMemos.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.trashedMemos.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_trashed_memos', function ($viewMemos) use ($memos) {
            return $viewMemos->count() === 3 && $viewMemos->first()->user_id === $memos->first()->user_id;
        });
    }

    /**
     * ソフトデリートしたメモが正しく元に戻されることをテスト
     * @return void
     */
    public function testUndoTrashedMemoController()
    {
        // ソフトデリートされたメモを作成
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);

        // undoメソッドを呼び出してレスポンスを確認
        $response = $this->patch(route('user.trashed-memo.undo'), ['memoId' => $memo->id]);

        // メモが元に戻されたことを確認
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'deleted_at' => null,
        ]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.trashed-memo.index'));
        $response->assertSessionHas(['message' => 'メモを元に戻しました。', 'status' => 'info']);
    }

    /**
     * ソフトデリートしたメモが正しく完全削除されることをテスト
     * @return void
     */
    public function testDestroyTrashedMemoController()
    {
        // ソフトデリートされたメモを作成
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);

        // destroyメソッドを呼び出してレスポンスを確認
        $response = $this->delete(route('user.trashed-memo.destroy'), ['memoId' => $memo->id]);

        // メモが完全に削除されたことを確認
        $this->assertDatabaseMissing('memos', ['id' => $memo->id]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.trashed-memo.index'));
        $response->assertSessionHas(['message' => 'メモを完全に削除しました。', 'status' => 'alert']);
    }
}
