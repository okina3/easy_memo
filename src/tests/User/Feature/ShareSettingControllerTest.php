<?php

namespace Tests\User\Feature;

use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\User\TestCase;

class ShareSettingControllerTest extends TestCase
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
     * 自分に共有されているメモを作成するヘルパーメソッド
     * @param User $sharingUser 共有元のユーザー
     * @param int $memoCount 作成するメモの数
     * @return Collection 作成されたメモのコレクション
     */
    private function createSharedMemo(User $sharingUser, int $memoCount = 1): Collection
    {
        // メモを作成
        $memos = Memo::factory()->count($memoCount)->create(['user_id' => $sharingUser->id]);

        // 共有設定を作成
        foreach ($memos as $memo) {
            ShareSetting::factory()->create([
                'sharing_user_id' => $this->user->id,
                'memo_id' => $memo->id,
                // 'edit_access' => false
                'edit_access' => true
            ]);
        }

        // 作成されたメモを返す
        return $memos;
    }

    /**
     * メモにタグと画像を関連付けるヘルパーメソッド
     * @param Memo $memo 関連付けるメモ
     * @param int $tagCount 作成するタグの数
     * @param int $imageCount 作成する画像の数
     * @return array タグと画像の配列
     */
    private function attachTagsAndImages(Memo $memo, int $tagCount = 3, int $imageCount = 2): array
    {
        // タグを作成し、メモに関連付け
        $tags = Tag::factory()->count($tagCount)->create();
        $memo->tags()->attach($tags);

        // 画像を作成し、メモに関連付け
        $images = Image::factory()->count($imageCount)->create();
        $memo->images()->attach($images);

        // タグと画像を返す
        return [$tags, $images];
    }

    /**
     * 共有メモの一覧表示が、正しく動作することをテスト
     * @return void
     */
    public function testIndexShareSettingController()
    {
        // 他のユーザーを作成
        $otherUsers = User::factory()->count(5)->create();
        // 自分に共有されている他人のメモを作成
        $otherUserMemos = $otherUsers->flatMap(function ($otherUsers) {
            return $this->createSharedMemo($otherUsers);
        });

        // indexメソッドを呼び出してレスポンスを確認
        $response = $this->get(route('user.share-setting.index'));

        // レスポンスが 'user.shareSettings.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.shareSettings.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('shared_memos', function ($viewMemos) use ($otherUserMemos) {
            // ビューから取得したメモをコレクションに変換
            $viewMemos = collect($viewMemos);
            // メモの数が期待通りかを確認。メモのIDが期待通りかを確認
            return $viewMemos->count() === $otherUserMemos->count() &&
                $viewMemos->pluck('id')->sort()->values()->all() === $otherUserMemos->pluck('id')->sort()->values()->all();
        });

        $response->assertViewHas('shared_users', function ($viewUsers) use ($otherUserMemos) {
            // ビューから取得したユーザーをコレクションに変換
            $viewUsers = collect($viewUsers);
            // メモに関連するユーザーを一意に取得
            $sharedUsers = $otherUserMemos->pluck('user')->unique('id');
            // ユーザーの数が期待通りかを確認。ユーザーのIDが期待通りかを確認
            return $viewUsers->count() === $sharedUsers->count() &&
                $viewUsers->pluck('id')->sort()->values()->all() === $sharedUsers->pluck('id')->sort()->values()->all();
        });
    }

    /**
     * メモの共有設定が、正しく保存されることをテスト
     * @return void
     */
    public function testStoreShareSettingController()
    {
        // メモと共有するユーザーを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $sharedUser = User::factory()->create();

        // リクエストデータを作成
        $requestData = [
            'share_user_start' => $sharedUser->email,
            'memoId' => $memo->id,
            'edit_access' => true,
        ];

        // メモ共有メソッドを呼び出してレスポンスを確認
        $response = $this->post(route('user.share-setting.store'), $requestData);

        // DBに共有設定が保存されているか確認
        $this->assertDatabaseHas('share_settings', [
            'sharing_user_id' => $sharedUser->id,
            'memo_id' => $memo->id,
            'edit_access' => true,
        ]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを共有しました。', 'status' => 'info']);
    }

    /**
     * 共有メモの詳細表示が、正しく動作することをテスト
     * @return void
     */
    public function testShowShareSettingController()
    {
        // 他のユーザーを作成
        $otherUser = User::factory()->create();
        // 自分に共有されている他人のメモを作成
        $memo = $this->createSharedMemo($otherUser)->first();
        // タグと画像を関連付け
        [$tags, $images] = $this->attachTagsAndImages($memo);

        // 共有メモ詳細表示メソッドを呼び出してレスポンスを確認
        $response = $this->get(route('user.share-setting.show', ['share' => $memo->id]));

        // レスポンスが 'user.shareSettings.show' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.shareSettings.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_memo', function ($viewMemo) use ($memo) {
            return $viewMemo->id === $memo->id;
        });

        $response->assertViewHas('get_memo_tags_name', function ($viewTags) use ($tags) {
            return collect($viewTags)->sort()->values()->all() === $tags->pluck('name')->sort()->values()->all();
        });

        $response->assertViewHas('get_memo_images', function ($viewImages) use ($images) {
            return collect($viewImages)->pluck('id')->sort()->values()->all() === $images->pluck('id')->sort()->values()->all();
        });

        $response->assertViewHas('select_user', function ($viewUser) use ($otherUser) {
            return $viewUser->id === $otherUser->id;
        });
    }

    /**
     * 共有メモの編集表示が、正しく動作することをテスト
     * @return void
     */
    public function testEditShareSettingController()
    {
        // 他のユーザーを作成
        $otherUser = User::factory()->create();
        // 自分に共有されている他人のメモを作成
        $memo = $this->createSharedMemo($otherUser)->first();
        // タグと画像を関連付け
        [$tags, $images] = $this->attachTagsAndImages($memo);

        // 共有メモ編集画面表示メソッドを呼び出してレスポンスを確認
        $response = $this->get(route('user.share-setting.edit', ['share' => $memo->id]));

        // レスポンスが 'user.shareSettings.edit' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.shareSettings.edit');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_memo', function ($viewMemo) use ($memo) {
            return $viewMemo->id === $memo->id;
        });

        $response->assertViewHas('get_memo_tags_name', function ($viewTags) use ($tags) {
            return collect($viewTags)->sort()->values()->all() === $tags->pluck('name')->sort()->values()->all();
        });

        $response->assertViewHas('get_memo_images', function ($viewImages) use ($images) {
            return collect($viewImages)->pluck('id')->sort()->values()->all() === $images->pluck('id')->sort()->values()->all();
        });

        $response->assertViewHas('select_user', function ($viewUser) use ($otherUser) {
            return $viewUser->id === $otherUser->id;
        });
    }

    /**
     * 共有メモが、正しく更新されることをテスト
     * @return void
     */
    public function testUpdateShareSettingController()
    {
        // メモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // リクエストデータを作成
        $requestData = [
            'memoId' => $memo->id,
            'content' => '更新内容',
        ];

        // 共有メモ更新メソッドを呼び出してレスポンスを確認
        $response = $this->patch(route('user.share-setting.update'), $requestData);

        // メモが更新されたことを確認
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'content' => '更新内容',
            'user_id' => $this->user->id,
        ]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.share-setting.index'));
        $response->assertSessionHas(['message' => '共有されたメモを更新しました。', 'status' => 'info']);
    }

    /**
     * 共有メモが、正しく削除されることをテスト
     * @return void
     */
    public function testDestroyShareSettingController()
    {
        // メモと共有するユーザーを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $sharedUser = User::factory()->create();

        // 共有設定を作成
        ShareSetting::factory()->create([
            'sharing_user_id' => $sharedUser->id,
            'memo_id' => $memo->id,
        ]);

        // リクエストデータを作成
        $requestData = [
            'share_user_end' => $sharedUser->email,
            'memoId' => $memo->id,
        ];

        // メモ共有解除メソッドを呼び出してレスポンスを確認
        $response = $this->delete(route('user.share-setting.destroy'), $requestData);

        // DBから共有設定が削除されているか確認
        $this->assertDatabaseMissing('share_settings', [
            'sharing_user_id' => $sharedUser->id,
            'memo_id' => $memo->id,
        ]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => '共有を解除しました。', 'status' => 'alert']);
    }
}
