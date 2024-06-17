<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\User\TestCase;

class ShareSettingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private ShareSetting $shareSetting;

    /**
     * テスト前の初期設定
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー、メモ、共有設定を作成
        // ２人のテスト用のユーザーを生成
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        // ２人のテスト用のメモを生成
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->otherUserMemo = Memo::factory()->create(['user_id' => $this->otherUser->id]);

        // ２人のテスト用の共有設定を生成
        // userの共有設定の作成（userは、otherUserに、メモを共有）
        $this->userShareSetting = ShareSetting::factory()->create([
            'sharing_user_id' => $this->otherUser->id,
            'memo_id' => $this->memo->id,
            'edit_access' => true
        ]);

        // otherUserの共有設定の作成（otherUserは、userに、メモを共有）
        $this->otherUserShareSetting = ShareSetting::factory()->create([
            'sharing_user_id' => $this->user->id,
            'memo_id' => $this->otherUserMemo->id,
            'edit_access' => true
        ]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);
    }

    /**
     * ShareSettingモデルの基本的なリレーションが正しく機能しているかのテスト
     * @return void
     */
    public function testShareSettingAttributesAndRelations()
    {
        // 共有設定に関連付けられたメモのインスタンスが正しいかを確認
        $this->assertInstanceOf(Memo::class, $this->userShareSetting->memo);
        // 共有設定に関連付けられたメモのIDが、正しいかを確認
        $this->assertEquals($this->memo->id, $this->userShareSetting->memo->id);

        // 共有設定に関連付けられたユーザーのインスタンスが正しいかを確認
        $this->assertInstanceOf(User::class, $this->userShareSetting->user);
        // sharing_user_idが、正しいユーザーIDであることを確認
        $this->assertEquals($this->otherUser->id, $this->userShareSetting->sharing_user_id);
    }

    /**
     * 共有された全てのメモを取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllSharedMemosScope()
    {
        $sharedMemos = ShareSetting::availableAllSharedMemos()->get();

        // 取得した共有設定の中に、テスト用共有設定が含まれているか確認
        $this->assertTrue($sharedMemos->contains($this->otherUserShareSetting));
    }

    /**
     * 選択した共有設定を取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectSettingScope()
    {
        $selectedSetting = ShareSetting::availableSelectSetting($this->otherUser->id, $this->memo->id)->first();

        // 取得した共有設定のIDが、テスト用の共有設定のIDと一致するか確認
        $this->assertEquals($this->userShareSetting->id, $selectedSetting->id);
    }

    /**
     * 共有設定をDBに保存するスコープのテスト
     * @return void
     */
    public function testAvailableCreateSettingScope()
    {
        // 新しい共有設定のデータを用意
        $data = new Request(['memoId' => $this->memo->id, 'edit_access' => true]);

        // スコープを使用して新しい共有設定を作成
        ShareSetting::availableCreateSetting($data, $this->user->id);

        // 作成された共有設定がDBに存在するかを確認
        $this->assertDatabaseHas('share_settings', [
            'sharing_user_id' => $this->user->id,
            'memo_id' => $this->memo->id,
            'edit_access' => true
        ]);
    }

    /**
     * 共有されていないメモに対する設定のチェックスコープのテスト
     * @return void
     */
    public function testAvailableCheckSettingScope()
    {
        $checkedSetting = ShareSetting::availableCheckSetting($this->otherUserMemo->id)->first();

        // チェックした設定のメモのIDが、テスト用のメモのIDと一致するか確認
        $this->assertEquals($this->otherUserMemo->id, $checkedSetting->memo_id);
    }

    /**
     * 自分が共有しているメモの共有情報を取得するスコープのテスト
     * @return void
     */
    public function testAvailableSharedMemoInfoScope()
    {
        $sharedInfo = ShareSetting::availableSharedMemoInfo($this->memo->id)->first();

        // 取得した共有情報のsharing_user_idが、テスト用のユーザーIDであることを確認
        $this->assertEquals($this->otherUser->id, $sharedInfo->sharing_user_id);
    }
}