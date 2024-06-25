<?php

namespace Tests\User\Unit\Services;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private ShareSetting $userShareSetting;
    private User $anotherUser;
    private Memo $anotherMemo;
    private ShareSetting $anotherUserShareSetting;
    private User $otherUser;
    private Memo $otherMemo;
    private ShareSetting $otherUserShareSetting;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // ３人のテスト用のユーザーを生成
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->otherUser = User::factory()->create();

        // ３人のテスト用のメモを生成
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->anotherMemo = Memo::factory()->create(['user_id' => $this->anotherUser->id]);
        $this->otherMemo = Memo::factory()->create(['user_id' => $this->otherUser->id]);

        // ３人のテスト用の共有設定を生成
        // userは、anotherUserに、メモを共有
        $this->userShareSetting = ShareSetting::factory()->create([
            'memo_id' => $this->memo->id,
            'sharing_user_id' => $this->anotherUser->id
        ]);

        // anotherUserは、userに、メモを共有
        $this->anotherUserShareSetting = ShareSetting::factory()->create([
            'memo_id' => $this->anotherMemo->id,
            'sharing_user_id' => $this->user->id
        ]);

        // otherUserは、anotherUserに、メモを共有
        $this->otherUserShareSetting = ShareSetting::factory()->create([
            'memo_id' => $this->otherMemo->id,
            'sharing_user_id' => $this->anotherUser->id
        ]);
    }

    /**
     * 停止ユーザーの共有設定を解除するメソッドのテスト。
     */
    public function testDeleteUserShareSettingAll()
    {
        // userの共有設定を削除するメソッドを実行
        UserService::deleteUserShareSettingAll($this->user->id);

        // userの共有設定が、削除されたことを確認
        $this->assertDatabaseMissing('share_settings', ['id' => $this->userShareSetting->id]);
        // userへの共有設定が、削除されたことを確認
        $this->assertDatabaseMissing('share_settings', ['id' => $this->anotherUserShareSetting->id]);
        // 他のユーザーの共有設定が影響を受けていないことを確認する
        $this->assertDatabaseHas('share_settings', ['id' => $this->otherUserShareSetting->id]);
    }
}
