<?php

namespace Tests\User\Feature;

use App\Models\Image;
use App\Models\Memo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\User\TestCase;

class MemoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メモの新規作成画面が、正しく表示されることをテスト
     * @return void
     */
    public function testCreateMemoController()
    {
        // ログインユーザーを作成して認証
        $user = User::factory()->create();
        $this->actingAs($user);

        // タグと画像を作成
        Tag::factory()->count(5)->create(['user_id' => $user->id]);
        Image::factory()->count(3)->create(['user_id' => $user->id]);

        // createメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.create'));

        // レスポンスが 'user.memos.create' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.memos.create');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_tags', function ($tags) use ($user) {
            return $tags->count() === 5 && $tags->first()->user_id === $user->id;
        });
        $response->assertViewHas('all_images', function ($images) use ($user) {
            return $images->count() === 3 && $images->first()->user_id === $user->id;
        });
    }

    /**
     * メモが、正しく保存されることをテスト
     * @return void
     */
    public function testStoreMemoController()
    {
        // ログインユーザーを作成して認証
        $user = User::factory()->create();
        $this->actingAs($user);

        // 新規タグと既存タグ、画像を作成
        $newTag = 'テスト、新規タグ';
        $existingTags = Tag::factory()->count(2)->create(['user_id' => $user->id]);
        $images = Image::factory()->count(2)->create(['user_id' => $user->id]);

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
        $this->assertDatabaseHas('memos', ['title' => 'テスト、メモ', 'content' => 'テスト、メモ内容', 'user_id' => $user->id,]);

        // 新規タグが保存されたことを確認
        $this->assertDatabaseHas('tags', ['name' => 'テスト、新規タグ', 'user_id' => $user->id,]);

        // 中間テーブルにタグと画像の紐付けが保存されたことを確認
        $memo = Memo::where('title', 'テスト、メモ')->first();
        foreach ($existingTags as $tag) {
            $this->assertDatabaseHas('memo_tags', ['memo_id' => $memo->id, 'tag_id' => $tag->id,]);
        }
        foreach ($images as $image) {
            $this->assertDatabaseHas('memo_images', ['memo_id' => $memo->id, 'image_id' => $image->id,]);
        }

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを登録しました。', 'status' => 'info']);
    }
}
