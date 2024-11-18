<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class TagTest extends TestCase
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
     * タグにメモを関連付けるヘルパーメソッド
     * @param Tag $tag 関連付けるタグ
     * @param int $memoCount 作成するメモの数
     * @return Collection 作成されたメモのコレクション
     */
    private function attachMemos(Tag $tag, int $memoCount): Collection
    {
        // メモを作成し、タグに関連付け
        $memos = Memo::factory()->count($memoCount)->create();
        $tag->memos()->attach($memos);

        // 作成されたメモのコレクションを返す
        return $memos;
    }

    /**
     * 基本的なリレーションが、正しく機能しているかのテスト
     * @return void
     */
    public function testTagAttributesAndRelations()
    {
        // 1件のタグを作成
        $tag = $this->createTags(1)->first();
        // タグに2件のメモを関連付け
        $attachedMemos = $this->attachMemos($tag, 2);

        // タグとメモのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $tag->memos());
        // 作成した関連付けられたメモのID配列が、作成したタグに紐づいたメモのID配列と、一致しているかを確認
        $this->assertEquals($attachedMemos->pluck('id')->toArray(), $tag->memos->pluck('id')->toArray());

        // タグとユーザーのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $tag->user());
        // 自分のユーザーのIDが、作成したタグに紐づいたユーザーのIDと、一致しているかを確認
        $this->assertEquals($this->user->id, $tag->user->id);
    }

    /**
     * 自分自身の全てのタグを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllTagsScope()
    {
        // 3件のタグを作成
        $tags = $this->createTags(3);
        // 全てのタグを取得
        $allTags = Tag::availableAllTags()->get();

        // 作成したタグのIDの配列が、取得したタグのIDの配列と、一致するか確認
        $this->assertEquals($tags->pluck('id')->toArray(), $allTags->pluck('id')->toArray());
    }

    /**
     * 自分自身の選択したタグを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectTagScope()
    {
        // 1件のタグを作成
        $tag = $this->createTags(1)->first();
        // 選択したタグを取得
        $selectedTag = Tag::availableSelectTag($tag->id)->first();

        // 作成したタグのIDが、取得したタグのIDと、一致するか確認
        $this->assertEquals($tag->id, $selectedTag->id);
    }

    /**
     * タグをDBに、保存するスコープのテスト
     * @return void
     */
    public function testAvailableCreateTagScope()
    {
        // 新しいタグ名を作成
        $tagName = '新しいテストタグ';
        // 新しいタグを保存
        Tag::availableCreateTag($tagName);

        // 作成されたタグがDBに存在するかを確認
        $this->assertDatabaseHas('tags', ['name' => $tagName, 'user_id' => $this->user->id,]);
    }

    /**
     * タグの重複を、チェックするスコープのテスト
     * @return void
     */
    public function testAvailableCheckDuplicateTagScope()
    {
        // 1件のタグを作成
        $tag = $this->createTags(1)->first();
        // 重複チェックのスコープでタグを取得
        $duplicateTag = Tag::availableCheckDuplicateTag($tag->name)->first();

        // 取得した重複タグが、存在するかを確認
        $this->assertNotNull($duplicateTag);
        // 作成したタグの名前が、取得した重複タグの名前と、一致するかを確認
        $this->assertEquals($tag->name, $duplicateTag->name);
    }
}
