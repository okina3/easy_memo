<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\User\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private Tag $tag;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー、メモ、タグを作成
        $this->user = User::factory()->create();
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->tag = Tag::factory()->create(['user_id' => $this->user->id]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);

        // リレーションを設定
        $this->tag->memos()->attach($this->memo);
    }

    /**
     * Tagモデルの基本的なリレーションが正しく機能しているかのテスト
     * @return void
     */
    public function testTagAttributesAndRelations()
    {
        // タグに関連付けられたメモが、正しいかを確認
        $this->assertTrue($this->tag->memos->contains($this->memo));
        // タグに関連付けられたメモのIDが、正しいかを確認
        $this->assertEquals($this->memo->id, $this->tag->memos->first()->id);

        // タグに関連付けられたユーザーが、正しいかを確認
        $this->assertInstanceOf(User::class, $this->tag->user);
        // タグに関連付けられたユーザーのIDが、正しいかを確認
        $this->assertEquals($this->user->id, $this->tag->user->id);
    }

    /**
     * 自分自身の全てのタグを取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllTagsScope()
    {
        $tags = Tag::availableAllTags()->get();

        // 取得したタグの中に、テスト用タグが含まれているかを確認
        $this->assertTrue($tags->contains($this->tag));
    }

    /**
     * 自分自身の選択したタグを取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectTagScope()
    {
        $selectedTag = Tag::availableSelectTag($this->tag->id)->first();

        // 取得したタグのIDが、テスト用のタグIDと一致するか確認
        $this->assertEquals($this->tag->id, $selectedTag->id);
    }

    /**
     * タグをDBに保存するスコープのテスト
     * @return void
     */
    public function testAvailableCreateTagScope()
    {
        // 新しいタグ名を設定
        $tagName = '新しいタグ';
        // 新しいタグを作成
        Tag::availableCreateTag($tagName);

        // 作成されたタグがDBに存在するかを確認
        $this->assertDatabaseHas('tags', ['name' => $tagName, 'user_id' => $this->user->id,]);
    }

    /**
     * タグの重複をチェックするスコープのテスト
     * @return void
     */
    public function testAvailableCheckDuplicateTagScope()
    {
        // 重複チェック用のタグ名を設定
        $tagName = '非重複タグ';
        // タグを作成
        Tag::factory()->create(['name' => $tagName, 'user_id' => $this->user->id]);
        // 重複チェックのスコープでタグを取得
        $duplicateTag = Tag::availableCheckDuplicateTag($tagName)->first();

        // 取得した重複タグが存在するかを確認
        $this->assertNotNull($duplicateTag);
        // 取得した重複タグの名前が、テスト用のタグの名前と一致するかを確認
        $this->assertEquals($tagName, $duplicateTag->name);
    }
}
