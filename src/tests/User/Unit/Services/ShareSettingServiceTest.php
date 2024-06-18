<?php

namespace Tests\User\Unit\Services;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use App\Services\ShareSettingService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class ShareSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private ShareSetting $shareSetting;

    /**
     * テスト前の初期設定
     */
    public function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー、メモ、共有設定を作成。
        $this->user = User::factory()->create();
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->shareSetting = ShareSetting::create([
            'sharing_user_id' => $this->user->id,
            'memo_id' => $this->memo->id,
            'edit_access' => true
        ]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);
    }

    /**
     * 特定のユーザーに共有されているメモを検索するテスト。
     */
    public function testSearchSharedMemosWithSpecificUser()
    {
        // 共有設定を作成
        $shareSettings = new Collection([
            new ShareSetting(['memo_id' => $this->memo->id, 'sharing_user_id' => $this->user->id, 'edit_access' => true])]);

        // リクエストにユーザー情報を追加
        \Request::merge(['user' => encrypt($this->user->id)]);
        // 共有メモを検索
        $result = ShareSettingService::searchSharedMemos($shareSettings);

        // 検索結果が1件であることを確認
        $this->assertCount(1, $result);
        // 検索結果のメモIDが、期待するメモIDと一致することを確認
        $this->assertEquals($this->memo->id, $result[0]->id);
        // 検索結果のメモが編集可能であることを確認
        $this->assertTrue($result[0]->access);
    }

    /**
     * 特定のユーザーを指定せずに共有されているメモを検索するテスト。
     */
    public function testSearchSharedMemosWithoutSpecificUser()
    {
        // 共有設定を作成
        $shareSettings = new Collection([
            new ShareSetting(['memo_id' => $this->memo->id, 'sharing_user_id' => $this->user->id, 'edit_access' => false])]);

        // 共有メモを検索
        $result = ShareSettingService::searchSharedMemos($shareSettings);

        // 検索結果が1件であることを確認
        $this->assertCount(1, $result);
        // 検索結果のメモIDが、期待するメモIDと一致することを確認
        $this->assertEquals($this->memo->id, $result[0]->id);
        // 検索結果のメモが編集可能であることを確認
        $this->assertFalse($result[0]->access);
    }

    /**
     * メモを共有しているユーザーを検索するテスト。
     */
    public function testSearchSharedUsers()
    {
        // 共有設定を作成
        $shareSettings = new Collection([
            new ShareSetting(['memo_id' => $this->memo->id, 'sharing_user_id' => $this->user->id])]);

        // 共有ユーザーを検索
        $result = ShareSettingService::searchSharedUser($shareSettings);

        // 検索結果の検証
        $this->assertCount(1, $result);
        $this->assertEquals($this->user->id, $result[0]->id);
    }

    /**
     * 共有設定の重複削除機能のテスト。
     */
    public function testResetDuplicateShareSettings()
    {
        // 重複した共有設定を削除
        ShareSettingService::resetDuplicateShareSettings($this->memo->id, $this->user->id);

        // 共有設定が削除されたことを検証
        $this->assertEquals(0, ShareSetting::count());
    }

    /**
     * 共有設定が存在しない場合のcheckSharedMemoShowメソッドの挙動をテストします。
     */
    public function testCheckSharedMemoShow()
    {
        // 必要な共有設定を削除
        ShareSetting::where('memo_id', $this->memo->id)->delete();

        // 例外の発生を期待
        $this->expectException(NotFoundHttpException::class);

        // 共有メモを表示しようとする
        ShareSettingService::checkSharedMemoShow($this->memo->id);
    }

    /**
     * 共有メモの編集制限機能のテスト。
     */
    public function testCheckSharedMemoEdit()
    {
        // 編集不可の共有設定を削除
        ShareSetting::where('memo_id', $this->memo->id)->delete();

        // 編集不可の共有設定を作成
        ShareSetting::create([
            'sharing_user_id' => $this->user->id,
            'memo_id' => $this->memo->id,
            'edit_access' => false // 編集不可
        ]);

        // 例外の発生を期待
        $this->expectException(NotFoundHttpException::class);

        // 共有メモを編集しようとする
        ShareSettingService::checkSharedMemoEdit($this->memo->id);
    }

    /**
     * 共有されているメモの共有状態の情報が正しく取得されることをテスト。
     */
    public function testCheckSharedMemoStatusWithSharedUsers()
    {
        // 共有メモの共有状態を取得
        $sharedUsers = ShareSettingService::checkSharedMemoStatus($this->memo->id);

        // 検証
        $this->assertCount(1, $sharedUsers);
        $this->assertEquals($this->user->id, $sharedUsers[0]->id);
        $this->assertEquals(1, $sharedUsers[0]->access);
    }

    /**
     * 選択したメモの全共有設定の削除のテスト。
     */
    public function testDeleteShareSettingAll()
    {
        // 全共有設定を削除
        ShareSettingService::deleteShareSettingAll($this->memo->id);

        // データベースから共有設定が削除されたことを検証
        $this->assertDatabaseMissing('share_settings', ['memo_id' => $this->memo->id]);
    }
}