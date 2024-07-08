<?php

namespace Tests\User\Unit\Services;

use App\Models\Memo;
use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;
use Illuminate\Database\Eloquent\Collection;

class TagServiceTest extends TestCase
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
        // 2人目の別のユーザーを作成
        $this->secondaryUser = User::factory()->create();

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
     * メモにタグと画像を関連付けるヘルパーメソッド
     * @param Memo $memo 関連付けるメモ
     * @param int $tagCount 作成するタグの数
     * @return Collection Collection 作成されたタグのコレクション
     */
    private function attachTagsAndImages(Memo $memo, int $tagCount): Collection
    {
        // タグを作成し、メモに関連付け
        $tags = Tag::factory()->count($tagCount)->create();
        $memo->tags()->attach($tags);

        // タグと画像を返す
        return $tags;
    }

    /**
     * 新規タグの保存と更新のテスト。
     */
    public function testStoreNewTag()
    {
        // 1件の自分のメモを作成
        $memo = $this->createMemos(1)->first();
        // 新規タグの入力
        $newTagName = '新しいタグ';
        // 新規タグを保存するサービスメソッドを実行
        TagService::storeNewTag($newTagName, $memo->id);

        // タグが保存されていることを確認
        $this->assertDatabaseHas('tags', ['name' => $newTagName, 'user_id' => $this->user->id,]);
    }

    /**
     * メモに紐づいたタグのIDを取得するのテスト。
     */
    public function testGetMemoTagsId()
    {
        // 1件の自分のメモを作成
        $memo = $this->createMemos(1)->first();
        // メモに2件のタグを関連付け
        $attachedTags = $this->attachTagsAndImages($memo, 2);
        // メモに紐づいたタグのIDを取得するサービスメソッドを実行
        $memoTagsById = TagService::getMemoTagsId($attachedTags);

        // 作成した関連付けられたタグのID配列が、取得したメモに紐づいたタグのID配列と、一致しているかを確認
        $this->assertEquals($attachedTags->pluck('id')->toArray(), $memoTagsById);
    }

    /**
     * メモに紐づいたタグのNameを取得するのテスト。
     */
    public function testGetMemoTagsName()
    {
        // 1件の自分のメモを作成
        $memo = $this->createMemos(1)->first();
        // メモに2件のタグを関連付け
        $attachedTags = $this->attachTagsAndImages($memo, 2);
        // メモに紐づいたタグの名前を取得するサービスメソッドを実行
        $memoTagsByName = TagService::getMemoTagsName($attachedTags);

        // 作成した関連付けられたタグのName配列が、取得したメモに紐づいたタグのName配列と、一致しているかを確認
        $this->assertEquals($attachedTags->pluck('name')->toArray(), $memoTagsByName);
    }
}
