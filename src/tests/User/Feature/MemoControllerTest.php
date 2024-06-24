<?php

namespace Tests\User\Feature;

use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\User\TestCase;

class MemoControllerTest extends TestCase
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
     * constructメソッドが正しく動作することをテスト
     * @return void
     */
    public function testConstructMemoController()
    {
        // 別のユーザーを作成
        $anotherUser = User::factory()->create();
        // 別のユーザーのメモを作成
        $anotherUserMemo = Memo::factory()->create(['user_id' => $anotherUser->id]);

        // constructメソッドが正しく動作して、別のユーザーのメモにアクセスできないことを確認
        $response = $this->get(route('user.show', $anotherUserMemo->id));
        $response->assertStatus(404);
    }

    /**
     * メモとタグの一覧が正しく表示されることをテスト
     * @return void
     */
    public function testIndexMemoController()
    {
        // タグとメモを作成
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);
        $memos = Memo::factory()->count(5)->create(['user_id' => $this->user->id]);

        // indexメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.index'));

        // レスポンスが 'user.memos.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_memos', function ($viewMemos) use ($memos) {
            return $viewMemos->count() === 5 && $viewMemos->first()->user_id === $memos->first()->user_id;
        });
        $response->assertViewHas('all_tags', function ($viewTags) use ($tags) {
            return $viewTags->count() === 3 && $viewTags->first()->user_id === $tags->first()->user_id;
        });
    }

    /**
     * メモの新規作成画面が、正しく表示されることをテスト
     * @return void
     */
    public function testCreateMemoController()
    {
        // タグと画像を作成
        Tag::factory()->count(5)->create(['user_id' => $this->user->id]);
        Image::factory()->count(3)->create(['user_id' => $this->user->id]);

        // createメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.create'));

        // レスポンスが 'user.memos.create' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.create');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_tags', function ($tags) {
            return $tags->count() === 5 && $tags->first()->user_id === $this->user->id;
        });
        $response->assertViewHas('all_images', function ($images) {
            return $images->count() === 3 && $images->first()->user_id === $this->user->id;
        });
    }

    /**
     * メモが、正しく保存されることをテスト
     * @return void
     */
    public function testStoreMemoController()
    {
        // 新規タグと既存タグ、画像を作成
        $newTag = 'テスト、新規タグ';
        $existingTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $images = Image::factory()->count(2)->create(['user_id' => $this->user->id]);

        // リクエストデータを作成
        $requestData = [
            'title' => 'テスト、メモ',
            'content' => 'テスト、メモ内容',
            'new_tag' => $newTag,
            'tags' => $existingTags->pluck('id')->toArray(),
            'images' => $images->pluck('id')->toArray(),
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // メモ保存メソッドを呼び出してレスポンスを確認
        $response = $this->post(route('user.store'), $requestData);

        // メモが保存されたことを確認
        $this->assertDatabaseHas('memos', [
            'title' => 'テスト、メモ',
            'content' => 'テスト、メモ内容',
            'user_id' => $this->user->id,
        ]);

        // 新規タグが保存されたことを確認
        $this->assertDatabaseHas('tags', ['name' => 'テスト、新規タグ', 'user_id' => $this->user->id]);

        // 中間テーブルにタグと画像の紐付けが保存されたことを確認
        $memo = Memo::where('title', 'テスト、メモ')->first();
        foreach ($existingTags as $tag) {
            $this->assertDatabaseHas('memo_tags', ['memo_id' => $memo->id, 'tag_id' => $tag->id]);
        }
        foreach ($images as $image) {
            $this->assertDatabaseHas('memo_images', ['memo_id' => $memo->id, 'image_id' => $image->id]);
        }

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを登録しました。', 'status' => 'info']);
    }

    /**
     * メモの詳細表示が正しく動作することをテスト
     * @return void
     */
    public function testShowMemoController()
    {
        // メモ、タグ、画像、共有設定を作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);
        $images = Image::factory()->count(2)->create(['user_id' => $this->user->id]);
        $memo->tags()->attach($tags);
        $memo->images()->attach($images);

        // メモ詳細表示メソッドを呼び出してレスポンスを確認
        $response = $this->get(route('user.show', ['memo' => $memo->id]));

        // レスポンスが 'user.memos.show' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_memo', function ($select_memo) use ($memo) {
            return $select_memo->id === $memo->id;
        });
        $response->assertViewHas('get_memo_tags', function ($get_memo_tags) use ($tags) {
            return count($get_memo_tags) === 3 && in_array($tags->first()->name, $get_memo_tags);
        });
        $response->assertViewHas('get_memo_images', function ($get_memo_images) use ($images) {
            return count($get_memo_images) === 2 && $get_memo_images[0]->id === $images->first()->id;
        });
        $response->assertViewHas('shared_users');
    }

    /**
     * メモの編集画面が、正しく表示されることをテスト
     * @return void
     */
    public function testEditMemoController()
    {
        // タグと画像、メモを作成
        $tags = Tag::factory()->count(5)->create(['user_id' => $this->user->id]);
        $images = Image::factory()->count(3)->create(['user_id' => $this->user->id]);
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // メモにタグと画像を関連付け
        $memo->tags()->attach($tags->pluck('id')->toArray());
        $memo->images()->attach($images->pluck('id')->toArray());

        // editメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.edit', ['memo' => $memo->id]));

        // レスポンスが 'user.memos.edit' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.edit');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_tags', function ($viewTags) use ($tags) {
            return $viewTags->pluck('id')->toArray() === $tags->pluck('id')->toArray();
        });
        $response->assertViewHas('all_images', function ($viewImages) use ($images) {
            return $viewImages->pluck('id')->toArray() === $images->pluck('id')->toArray();
        });
        $response->assertViewHas('select_memo', function ($viewMemo) use ($memo) {
            return $viewMemo->id === $memo->id;
        });
        $response->assertViewHas('get_memo_tags', function ($viewMemoTags) use ($tags) {
            return $viewMemoTags === $tags->pluck('id')->toArray();
        });
        $response->assertViewHas('get_memo_images_id', function ($viewMemoImagesId) use ($images) {
            return $viewMemoImagesId === $images->pluck('id')->toArray();
        });
        $response->assertViewHas('get_memo_images', function ($viewMemoImages) use ($images) {
            return collect($viewMemoImages)->pluck('id')->toArray() === $images->pluck('id')->toArray();
        });
    }

    /**
     * メモが、正しく更新されることをテスト
     * @return void
     */
    public function testUpdateMemoController()
    {
        // ログインユーザーを作成して認証
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // 既存のタグ、画像を作成
        $existingTags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $images = Image::factory()->count(2)->create(['user_id' => $this->user->id]);

        // リクエストデータを作成
        $requestData = [
            'memoId' => $memo->id,
            'title' => '更新テスト、メモ',
            'content' => '更新テスト、メモ内容',
            'new_tag' => '更新テスト、新規タグ',
            'tags' => $existingTags->pluck('id')->toArray(),
            'images' => $images->pluck('id')->toArray(),
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // メモ更新メソッドを呼び出してレスポンスを確認
        $response = $this->patch(route('user.update'), $requestData);

        // メモが更新されたことを確認
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'title' => '更新テスト、メモ',
            'content' => '更新テスト、メモ内容',
            'user_id' => $this->user->id,
        ]);

        // 新規タグが保存されたことを確認
        $this->assertDatabaseHas('tags', ['name' => '更新テスト、新規タグ', 'user_id' => $this->user->id]);

        // 中間テーブルにタグと画像の紐付けが保存されたことを確認
        foreach ($existingTags as $tag) {
            $this->assertDatabaseHas('memo_tags', ['memo_id' => $memo->id, 'tag_id' => $tag->id]);
        }
        foreach ($images as $image) {
            $this->assertDatabaseHas('memo_images', ['memo_id' => $memo->id, 'image_id' => $image->id]);
        }

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを更新しました。', 'status' => 'info']);
    }

    /**
     * メモが、正しく削除（ソフトデリート）されることをテスト
     * @return void
     */
    public function testDestroyMemoController()
    {
        // メモ、タグ、画像を作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        $images = Image::factory()->count(2)->create(['user_id' => $this->user->id]);
        // メモにタグと画像を紐付け
        $memo->tags()->attach($tags->pluck('id')->toArray());
        $memo->images()->attach($images->pluck('id')->toArray());
        // 共有設定を作成
        ShareSetting::factory()->count(2)->create(['memo_id' => $memo->id]);

        // リクエストデータを作成
        $requestData = ['memoId' => $memo->id];

        // メモ削除メソッドを呼び出してレスポンスを確認
        $response = $this->delete(route('user.destroy', $memo->id), $requestData);

        // メモがソフトデリートされたことを確認
        $this->assertSoftDeleted('memos', ['id' => $memo->id]);

        // 共有設定が削除されたことを確認
        foreach ($memo->shareSettings as $shareSetting) {
            $this->assertDatabaseMissing('share_settings', ['id' => $shareSetting->id]);
        }

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモをゴミ箱に移動しました。', 'status' => 'alert']);
    }
}
