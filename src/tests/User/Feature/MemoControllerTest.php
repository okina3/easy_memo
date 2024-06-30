<?php

namespace Tests\User\Feature;

use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Mockery;
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
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * メモを作成するヘルパーメソッド
     * @param int $count メモの作成数
     * @return Collection 作成されたメモのコレクション
     */
    private function createMemos(int $count): Collection
    {
        // 指定された数のメモを、現在のユーザーに関連付けて作成する
        return Memo::factory()->count($count)->create(['user_id' => $this->user->id]);
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
     * メモにタグと画像を関連付けるヘルパーメソッド
     * @param Memo $memo 関連付けるメモ
     * @param int $tagCount 作成するタグの数
     * @param int $imageCount 作成する画像の数
     * @return array タグと画像の配列
     */
    private function attachTagsAndImages(Memo $memo, int $tagCount, int $imageCount): array
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
     * メモを他人に共有する設定を作成するヘルパーメソッド
     * @param Memo $memo 関連付けるメモ
     * @param User $sharingUser 共有したいユーザー
     */
    private function createSharingMemo(Memo $memo, User $sharingUser)
    {
        // 共有設定を作成し、メモに関連付けて、値を返す
        return ShareSetting::factory()->create(['sharing_user_id' => $sharingUser->id, 'memo_id' => $memo->id,]);
    }

    /**
     * constructメソッドが正しく動作することをテスト
     * @return void
     */
    public function testConstructMemoController()
    {
        // 1件の別のユーザーを作成
        $anotherUser = User::factory()->create();
        // 1件の別のユーザーのメモを作成
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
        // 5件のメモを作成
        $memos = $this->createMemos(5);
        // 3件のタグを作成
        $tags = $this->createTags(3);

        // メモとタグの一覧を表示する為に、リクエストを送信
        $response = $this->get(route('user.index'));

        // レスポンスが 'user.memos.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_memos', function ($viewMemos) use ($memos) {
            // ビューから取得したメモをコレクションに変換
            $viewMemos = collect($viewMemos);
            // ビューに渡されるメモが、5件であり、かつ、作成したメモのID配列と一致することを確認
            return $viewMemos->count() === 5 && $viewMemos->pluck('id')->toArray() === $memos->pluck('id')->toArray();
        });
        $response->assertViewHas('all_tags', function ($viewTags) use ($tags) {
            // ビューから取得したタグをコレクションに変換
            $viewTags = collect($viewTags);
            // ビューに渡されるタグが、3件であり、かつ、作成したタグのID配列と一致することを確認
            return $viewTags->count() === 3 && $viewTags->pluck('id')->toArray() === $tags->pluck('id')->toArray();
        });
    }

    /**
     * メモの新規作成画面が、正しく表示されることをテスト
     * @return void
     */
    public function testCreateMemoController()
    {
        // 5件のタグを作成
        $tags = $this->createTags(5);
        // 3件の画像を作成
        $images = $this->createImages(3);

        // メモの新規作成画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.create'));

        // レスポンスが 'user.memos.create' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.create');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_tags', function ($viewTags) use ($tags) {
            // ビューから取得したタグをコレクションに変換
            $viewTags = collect($viewTags);
            // ビューに渡されるタグが、5件であり、かつ、作成したタグのID配列と一致することを確認
            return $viewTags->count() === 5 && $viewTags->pluck('id')->toArray() === $tags->pluck('id')->toArray();
        });
        $response->assertViewHas('all_images', function ($viewImages) use ($images) {
            // ビューから取得した画像をコレクションに変換
            $viewImages = collect($viewImages);
            // ビューに渡される画像が、3件であり、かつ、作成した画像のID配列と一致することを確認
            return $viewImages->count() === 3 && $viewImages->pluck('id')->toArray() === $images->pluck('id')->toArray();
        });
    }

    /**
     * メモが、正しく保存されることをテスト
     * @return void
     */
    public function testStoreMemoController()
    {
        // 2件の既存タグを作成
        $existingTags = $this->createTags(2);
        // 2件の画像を作成
        $images = $this->createImages(2);
        // 1件の新規タグを作成
        $newTag = 'テスト、新規タグ';

        // 保存するデータを作成
        $requestData = [
            'title' => 'テスト、メモ',
            'content' => 'テスト、メモ内容',
            'new_tag' => $newTag,
            'tags' => $existingTags->pluck('id')->toArray(),
            'images' => $images->pluck('id')->toArray(),
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // メモ保存のリクエストを送信
        $response = $this->post(route('user.store'), $requestData);

        // メモが保存されたことを確認
        $this->assertDatabaseHas('memos', [
            'title' => 'テスト、メモ', 'content' => 'テスト、メモ内容', 'user_id' => $this->user->id,
        ]);

        // 新規タグが保存されたことを確認
        $this->assertDatabaseHas('tags', ['name' => 'テスト、新規タグ', 'user_id' => $this->user->id]);

        // 中間テーブルにタグと画像の紐付けが保存されたことを確認
        $memo = Memo::where('title', 'テスト、メモ')->first();

        // 各既存タグがメモに関連付けられていることを確認
        foreach ($existingTags as $tag) {
            $this->assertDatabaseHas('memo_tags', ['memo_id' => $memo->id, 'tag_id' => $tag->id]);
        }
        // 各画像がメモに関連付けられていることを確認
        foreach ($images as $image) {
            $this->assertDatabaseHas('memo_images', ['memo_id' => $memo->id, 'image_id' => $image->id]);
        }

        // レスポンスが 'index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを登録しました。', 'status' => 'info']);
    }

    /**
     * メモが、正しく保存される時のエラーハンドリングをテスト
     * @return void
     */
    public function testErrorStoreMemoController()
    {
        // 保存するデータを作成
        $requestData = ['title' => 'テスト、メモ', 'content' => 'テスト、メモ内容',];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // DB::transactionメソッドが呼び出されると、一度だけ例外をスローするように設定
        DB::shouldReceive('transaction')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->with(Mockery::type(Exception::class));

        // 例外がスローされることを期待し、そのメッセージが"DBエラー"であることを確認
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('DBエラー');

        // メモ保存のリクエストを送信
        $this->post(route('user.store'), $requestData);
    }

    /**
     * メモの詳細表示が正しく動作することをテスト
     * @return void
     */
    public function testShowMemoController()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // メモに3件のタグと2件の画像を関連付け
        [$attachedTags, $attachedImages] = $this->attachTagsAndImages($memo, 3, 2);

        // メモ詳細画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.show', ['memo' => $memo->id]));

        // レスポンスが 'user.memos.show' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_memo', function ($viewMemo) use ($memo) {
            // ビューに渡されるメモのIDが、作成したメモのIDと一致することを確認
            return $viewMemo->id === $memo->id;
        });
        $response->assertViewHas('get_memo_tags_name', function ($viewAchedTags) use ($attachedTags) {
            // ビューから取得したタグをコレクションに変換
            $viewAchedTags = collect($viewAchedTags);
            // ビューに渡されるタグが、3件であり、かつ、作成したタグのNameの配列と一致することを確認
            return $viewAchedTags->count() === 3 &&
                $viewAchedTags->toArray() === $attachedTags->pluck('name')->toArray();
        });
        $response->assertViewHas('get_memo_images', function ($viewAchedImages) use ($attachedImages) {
            // ビューから取得したタグをコレクションに変換
            $viewAchedImages = collect($viewAchedImages);
            // ビューに渡される画像が、2件であり、かつ、作成した画像のIDの配列と一致することを確認
            return $viewAchedImages->count() === 2 &&
                $viewAchedImages->pluck('id')->toArray() === $attachedImages->pluck('id')->toArray();
        });

        // shared_usersキーがビューに存在することを確認
        $response->assertViewHas('shared_users');
    }

    /**
     * メモの編集画面が、正しく表示されることをテスト
     * @return void
     */
    public function testEditMemoController()
    {
        // 3件のタグを作成
        $tags = $this->createTags(3);
        // 2件の画像を作成
        $images = $this->createImages(2);

        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // メモに5件のタグと3件の画像を関連付け
        [$attachedTags, $attachedImages] = $this->attachTagsAndImages($memo, 5, 3);

        // メモ編集画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.edit', ['memo' => $memo->id]));

        // レスポンスが 'user.memos.edit' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.edit');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_tags', function ($viewTags) use ($tags) {
            // ビューから取得したタグをコレクションに変換
            $viewTags = collect($viewTags);
            // ビューに渡されるタグが、3件であり、かつ、作成したタグのID配列と一致することを確認
            return $viewTags->count() === 3 && $viewTags->pluck('id')->toArray() === $tags->pluck('id')->toArray();
        });
        $response->assertViewHas('all_images', function ($viewImages) use ($images) {
            // ビューから取得した画像をコレクションに変換
            $viewImages = collect($viewImages);
            // ビューに渡される画像が、2件であり、かつ、作成した画像のID配列と一致することを確認
            return $viewImages->count() === 2 && $viewImages->pluck('id')->toArray() === $images->pluck('id')->toArray();
        });
        $response->assertViewHas('select_memo', function ($viewMemo) use ($memo) {
            // ビューに渡されるメモのIDが、作成したメモのIDと一致することを確認
            return $viewMemo->id === $memo->id;
        });
        $response->assertViewHas('get_memo_tags_id', function ($viewAchedTags) use ($attachedTags) {
            // ビューから取得した紐づいたタグをコレクションに変換
            $viewAchedTags = collect($viewAchedTags);
            // ビューに渡されるメモに紐づいたタグが、5件であり、かつ、作成した紐づいたタグのID配列と一致することを確認
            return $viewAchedTags->count() === 5 && $viewAchedTags->toArray() === $attachedTags->pluck('id')->toArray();
        });
        $response->assertViewHas('get_memo_images', function ($viewAchedImages) use ($attachedImages) {
            // ビューから取得した紐づいた画像をコレクションに変換
            $viewAchedImages = collect($viewAchedImages);
            // ビューに渡される画像が、3件であり、かつ、作成した紐づいた画像のID配列と一致することを確認
            return $viewAchedImages->count() === 3 &&
                $viewAchedImages->pluck('id')->toArray() === $attachedImages->pluck('id')->toArray();
        });
        $response->assertViewHas('get_memo_images_id', function ($viewAchedImagesId) use ($attachedImages) {
            // ビューから取得した画像をコレクションに変換
            $viewAchedImagesId = collect($viewAchedImagesId);
            // ビューに渡される画像が、3件であり、かつ、作成した画像のID配列と一致することを確認
            return $viewAchedImagesId->count() === 3 &&
                $viewAchedImagesId->toArray() === $attachedImages->pluck('id')->toArray();
        });
    }

    /**
     * メモが、正しく更新されることをテスト
     * @return void
     */
    public function testUpdateMemoController()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();

        // 2件のタグを作成
        $existingTags = $this->createTags(2);
        // 2件の画像を作成
        $images = $this->createImages(2);

        // 更新するデータを作成
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

        // メモを更新する為に、リクエストを送信
        $response = $this->patch(route('user.update'), $requestData);

        // メモが更新されたことを確認
        $this->assertDatabaseHas('memos', [
            'title' => '更新テスト、メモ', 'content' => '更新テスト、メモ内容', 'user_id' => $this->user->id,
        ]);

        // 新規タグが保存されたことを確認
        $this->assertDatabaseHas('tags', ['name' => '更新テスト、新規タグ', 'user_id' => $this->user->id]);

        // 中間テーブルに、タグの紐付けが保存されたことを確認
        foreach ($existingTags as $tag) {
            $this->assertDatabaseHas('memo_tags', ['memo_id' => $memo->id, 'tag_id' => $tag->id]);
        }

        // 中間テーブルに、画像の紐付けが保存されたことを確認
        foreach ($images as $image) {
            $this->assertDatabaseHas('memo_images', ['memo_id' => $memo->id, 'image_id' => $image->id]);
        }

        // レスポンスが 'index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを更新しました。', 'status' => 'info']);
    }

    /**
     * メモが、正しく更新される時のエラーハンドリングをテスト
     * @return void
     */
    public function testErrorUpdateMemoController()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();

        // 更新するデータを作成
        $requestData = [
            'memoId' => $memo->id,
            'title' => '更新テスト、メモ',
            'content' => '更新テスト、メモ内容',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // DB::transactionメソッドが呼び出されると、一度だけ例外をスローするように設定
        DB::shouldReceive('transaction')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->with(Mockery::type(Exception::class));

        // 例外がスローされることを期待し、そのメッセージが"DBエラー"であることを確認
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('DBエラー');

        // メモを更新する為に、リクエストを送信
        $this->patch(route('user.update'), $requestData);
    }

    /**
     * メモが、正しく削除（ソフトデリート）されることをテスト
     * @return void
     */
    public function testDestroyMemoController()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // 1件の別のユーザーを作成
        $anotherUser = User::factory()->create();
        // メモを他のユーザーに共有する設定を作成
        $ShareSettings = $this->createSharingMemo($memo, $anotherUser);

        // メモを削除（ソフトデリート）する為に、リクエストを送信
        $response = $this->delete(route('user.destroy', ['memoId' => $memo->id]));

        // メモが削除（ソフトデリート）されたことを確認
        $this->assertSoftDeleted('memos', ['id' => $memo->id]);

        // 紐づいた共有設定が削除されたことを確認
        $this->assertDatabaseMissing('share_settings', ['id' => $ShareSettings->id]);

        // レスポンスが 'index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモをゴミ箱に移動しました。', 'status' => 'alert']);
    }

    /**
     * メモが、正しく削除（ソフトデリート）される時のエラーハンドリングをテスト
     * @return void
     */
    public function testErrorDestroyMemoController()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // 1件の別のユーザーを作成
        $anotherUser = User::factory()->create();
        // メモを他のユーザーに共有する設定を作成
        $this->createSharingMemo($memo, $anotherUser);

        // DB::transactionメソッドが呼び出されると、一度だけ例外をスローするように設定
        DB::shouldReceive('transaction')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->with(Mockery::type(Exception::class));

        // 例外がスローされることを期待し、そのメッセージが"DBエラー"であることを確認
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('DBエラー');

        // メモを削除（ソフトデリート）する為に、リクエストを送信
        $this->delete(route('user.destroy', ['memoId' => $memo->id]));
    }
}
