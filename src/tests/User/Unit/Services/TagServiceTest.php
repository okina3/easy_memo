<?php

namespace Tests\User\Unit\Services;

use App\Models\Memo;
use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\User\TestCase;

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
        parent::setUp();
        // テスト用ユーザー、メモ、タグを作成
        $this->user = User::factory()->create();
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->tag = Tag::factory()->create(['user_id' => $this->user->id]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);

        // リレーションを設定
        $this->memo->tags()->attach($this->tag);
    }

    /**
     * 新規タグの保存と更新のテスト。
     */
    public function testStoreNewTag()
    {
        // 新規タグの入力
        $newTagName = '新しいタグ';

        // 新規タグを保存するリクエスト
        TagService::storeNewTag($newTagName, $this->memo->id);

        // タグが保存されていることを確認
        $this->assertDatabaseHas('tags', ['name' => $newTagName, 'user_id' => $this->user->id,]);
    }

    /**
     * メモに紐づいたタグの情報を取得するのテスト。
     */
    public function testGetMemoTags()
    {
        // メモに紐づいたタグのIDを取得
//        $memoTagsById = TagService::getMemoTags($this->memo->tags, 'id');
        $memoTagsById = TagService::getMemoTagsId($this->memo->tags);

        // メモに紐づいたタグの名前を取得
//        $memoTagsByName = TagService::getMemoTags($this->memo->tags, 'name');
        $memoTagsByName = TagService::getMemoTagsName($this->memo->tags);

        // タグIDが正しく含まれていることを確認
        $this->assertContains($this->tag->id, $memoTagsById);
        // タグ名が正しく含まれていることを確認
        $this->assertContains($this->tag->name, $memoTagsByName);
    }
}
