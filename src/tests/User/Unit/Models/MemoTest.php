<?php

namespace Tests\User\Unit\Models;

use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\User\TestCase;

class MemoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private Tag $tag;
    private Image $image;
    private ShareSetting $shareSetting;

    /**
     * テスト前の初期設定
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー、メモ、タグ、画像、共有設定を作成
        $this->user = User::factory()->create();
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $this->image = Image::factory()->create(['user_id' => $this->user->id]);
        $this->shareSetting = ShareSetting::factory()->create(['memo_id' => $this->memo->id]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);

        // リレーションを設定
        $this->memo->tags()->attach($this->tag);
        $this->memo->images()->attach($this->image);
    }

    /**
     * Memoモデルの基本的なリレーションが正しく機能しているかのテスト
     * @return void
     */
    public function testMemoRelations()
    {
        // メモに関連付けられたタグが、正しいかを確認
        $this->assertTrue($this->memo->tags->contains($this->tag));
        // メモに関連付けられたタグのIDが、正しいかを確認
        $this->assertEquals($this->tag->id, $this->memo->tags->first()->id);

        // メモに関連付けられた画像が、正しいかを確認
        $this->assertTrue($this->memo->images->contains($this->image));
        // メモに関連付けられた画像のIDが、正しいかを確認
        $this->assertEquals($this->image->id, $this->memo->images->first()->id);

        // メモに関連付けられた共有設定のインスタンスが正しいかを確認
        $this->assertInstanceOf(ShareSetting::class, $this->memo->shareSettings->first());
        // メモに関連付けられた共有設定のIDが、正しいかを確認
        $this->assertEquals($this->shareSetting->id, $this->memo->shareSettings->first()->id);

        // メモに関連付けられたユーザーが、正しいかを確認
        $this->assertInstanceOf(User::class, $this->memo->user);
        // メモに関連付けられたユーザーのIDが、正しいかを確認
        $this->assertEquals($this->user->id, $this->memo->user->id);
    }

    /**
     * 自分自身の全てのメモを取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllMemosScope()
    {
        $memos = Memo::availableAllMemos()->get();

        // 取得したメモの中に、テスト用メモが含まれているか確認
        $this->assertTrue($memos->contains($this->memo));
    }

    /**
     * 自分自身の選択したメモを取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectMemoScope()
    {
        $selectedMemo = Memo::availableSelectMemo($this->memo->id)->first();

        // 取得したメモのIDが、テスト用メモのIDと一致するか確認
        $this->assertEquals($this->memo->id, $selectedMemo->id);
    }

    /**
     * 自分自身の全ての削除済みのメモを取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllTrashedMemosScope()
    {
        $this->memo->delete();
        $trashedMemos = Memo::availableAllTrashedMemos()->get();

        // 取得した削除済みメモの中に、テスト用メモが含まれているか確認
        $this->assertTrue($trashedMemos->contains($this->memo));
    }

    /**
     * 自分自身の選択した削除済みのメモを取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectTrashedMemoScope()
    {
        $this->memo->delete();
        $selectedTrashedMemo = Memo::availableSelectTrashedMemo($this->memo->id)->first();

        // 取得した削除済みメモのIDが、テスト用メモのIDと一致するか確認
        $this->assertEquals($this->memo->id, $selectedTrashedMemo->id);
    }
}