<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
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
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * ソフトデリートされたメモを作成するヘルパーメソッド
     * @param int $count メモの作成数
     * @return Collection 作成されたメモのコレクション
     */
    private function createTrashedMemos(int $count): Collection
    {
        // 指定された数のソフトデリートされたメモを、現在のユーザーに関連付けて作成する
        return Memo::factory()->count($count)->create(['user_id' => $this->user->id, 'deleted_at' => now()]);
    }

    /**
     * ソフトデリートしたメモ一覧が、正しく表示されることをテスト
     * @return void
     */
    public function testIndexTrashedMemoController()
    {
        // 5件のソフトデリートされたメモを作成
        $memos = $this->createTrashedMemos(5);

        // ソフトデリートされたメモの一覧を表示する為に、リクエストを送信
        $response = $this->get(route('user.trashed-memo.index'));

        // レスポンスが 'user.trashedMemos.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.trashedMemos.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_trashed_memos', function ($viewTrashedMemos) use ($memos) {
            // ビューに渡される、メモが、5件であり、かつ、メモのID配列も、一致することを確認
            return $viewTrashedMemos->count() === 5 &&
                $viewTrashedMemos->pluck('id')->toArray() === $memos->pluck('id')->toArray();
        });
    }

    /**
     * ソフトデリートしたメモが、正しく元に戻されることをテスト
     * @return void
     */
    public function testUndoTrashedMemoController()
    {
        // 1件のソフトデリートされたメモを作成
        $memo = $this->createTrashedMemos(1)->first();

        // ソフトデリートしたメモを、元に戻す為に、リクエストを送信
        $response = $this->patch(route('user.trashed-memo.undo'), ['memoId' => $memo->id]);

        // メモが元に戻されたことを確認
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'deleted_at' => null,
        ]);

        // レスポンスが 'trashed-memo.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.trashed-memo.index'));
        $response->assertSessionHas(['message' => 'メモを元に戻しました。', 'status' => 'info']);
    }

    /**
     * ソフトデリートしたメモが、正しく完全削除されることをテスト
     * @return void
     */
    public function testDestroyTrashedMemoController()
    {
        // 1件のソフトデリートされたメモを作成
        $memo = $this->createTrashedMemos(1)->first();

        // ソフトデリートしたメモを、完全削除する為に、リクエストを送信
        $response = $this->delete(route('user.trashed-memo.destroy'), ['memoId' => $memo->id]);

        // メモが完全に削除されたことを確認
        $this->assertDatabaseMissing('memos', ['id' => $memo->id]);

        // レスポンスが 'trashed-memo.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.trashed-memo.index'));
        $response->assertSessionHas(['message' => 'メモを完全に削除しました。', 'status' => 'alert']);
    }
}
