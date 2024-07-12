<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\User\TestCase;

class ShareSettingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $secondaryUser;
    private Memo $memo;
    private Memo $secondaryUserMemo;

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
        // 3人目のユーザーを作成
        $this->tertiaryUser = User::factory()->create();

        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * メモを共有させる設定を作成するヘルパーメソッド
     * @param User $user メモの所有者
     * @param User $sharingUser 共有設定させたいユーザー
     * @param bool $editAccess 編集アクセスの許可（デフォルトは true）
     * @return ShareSetting 作成された共有設定のインスタンス
     */
    private function createShareSetting(User $user, User $sharingUser, bool $editAccess = true): ShareSetting
    {
        // メモを作成
        $memo = Memo::factory()->create(['user_id' => $user->id]);

        // 共有設定を作成
        return ShareSetting::factory()->create([
            // 共有させたいユーザー
            'sharing_user_id' => $sharingUser->id,
            // メモの選択
            'memo_id' => $memo->id,
            // 編集も可能
            'edit_access' => $editAccess
        ]);
    }

    /**
     * 基本的なリレーションが正しく機能しているかのテスト
     * @return void
     */
    public function testShareSettingAttributesAndRelations()
    {
        // 1件の共有設定を作成（自分のメモを、2人目のユーザーに共有）
        $shareSetting = $this->createShareSetting($this->user, $this->secondaryUser);

        // 共有設定とメモのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $shareSetting->memo());
        // 作成した共有設定のmemo_idが、共有設定に関連付けられたメモのIDと、一致しているかを確認
        $this->assertEquals($shareSetting->memo_id, $shareSetting->memo->id);

        // 共有設定とユーザーのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $shareSetting->user());
        // 作成した共有設定のsharing_user_idが、作成した共有設定に紐づいたユーザーのIDと、一致しているかを確認
        $this->assertEquals($shareSetting->sharing_user_id, $shareSetting->user->id);
    }

    /**
     * 共有された全てのメモを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllSharedMemosScope()
    {
        // 2件の他人のメモを、自分に共有する設定を作成
        $shareSettings = collect([
            $this->createShareSetting($this->secondaryUser, $this->user),
            $this->createShareSetting($this->tertiaryUser, $this->user)
        ]);
        // 全ての共有設定を取得
        $allSettings = ShareSetting::availableAllSharedMemos()->get();

        // 作成した共有設定のmemo_idの配列が、取得した共有設定のmemo_idの配列と、一致するか確認
        $this->assertEquals($shareSettings->pluck('memo_id')->toArray(), $allSettings->pluck('memo_id')->toArray());
    }

    /**
     * 選択した共有設定を、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectSettingScope()
    {
        // 1件の共有設定を作成（2人目のユーザーのメモを、自分に共有）
        $shareSetting = $this->createShareSetting($this->secondaryUser, $this->user);
        // 選択した共有設定を取得
        $selectedShareSetting =
            ShareSetting::availableSelectSetting($shareSetting->sharing_user_id, $shareSetting->memo_id)->first();

        // 作成した共有設定のmemo_idが、取得した共有設定のmemo_idと、一致するか確認
        $this->assertEquals($shareSetting->pluck('memo_id'), $selectedShareSetting->pluck('memo_id'));
    }

    /**
     * 共有設定をDBに、保存するスコープのテスト
     * @return void
     */
    public function testAvailableCreateSettingScope()
    {
        // 1件の自分のメモ作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // 1件の共有設定のデータを作成（自分のメモを、2人目のユーザーに共有）
        $requestData = new Request([
            'sharing_user_id' => $this->secondaryUser->id,
            'memoId' => $memo->id,
            'edit_access' => true
        ]);
        // 共有設定を保存
        ShareSetting::availableCreateSetting($requestData, $requestData->sharing_user_id);

        // 作成された共有設定が、DBに存在するかを確認
        $this->assertDatabaseHas('share_settings', [
            'sharing_user_id' => $this->secondaryUser->id,
            'memo_id' => $memo->id,
            'edit_access' => true
        ]);
    }

    /**
     * 共有されていないメモに対する設定の、チェックをするスコープのテスト（成功）
     * @return void
     */
    public function testAvailableCheckSettingScope()
    {
        // 1件の共有設定を作成（2人目のユーザーのメモを、自分に共有）
        $shareSetting = $this->createShareSetting($this->secondaryUser, $this->user);
        // 自分に共有されているメモの情報を取得
        $checkedSetting = ShareSetting::availableCheckSetting($shareSetting->memo_id)->first();

        // 作成した共有設定のmemo_idが、取得した共有設定のmemo_idと、一致するか確認
        $this->assertEquals($shareSetting->pluck('memo_id'), $checkedSetting->pluck('memo_id'));
    }

    /**
     * 共有されていないメモに対する設定の、チェックをするスコープのテスト（失敗）
     * @return void
     */
    public function testErrorAvailableCheckSettingScope()
    {
        // 1件の自分に共有されていない共有設定を作成（3人目のユーザーのメモを、2人目のユーザーに共有）
        $tertiaryUserSetting = $this->createShareSetting($this->tertiaryUser, $this->secondaryUser);
        // 自分に共有されていないメモの情報を取得
        $checkedSetting = ShareSetting::availableCheckSetting($tertiaryUserSetting->memo_id)->first();

        // 取得した情報がnull。自分に共有されていないメモの設定が、取得できていないことを確認
        $this->assertNull($checkedSetting);
    }

    /**
     * 自分が共有しているメモの共有情報を、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSharedMemoInfoScope()
    {
        // 1件の共有設定を作成（自分のメモを、2人目のユーザーに共有）
        $shareSetting = $this->createShareSetting($this->user, $this->secondaryUser);
        //  自分が共有しているメモの、共有情報を取得
        $sharedInfo = ShareSetting::availableSharedMemoInfo($shareSetting->memo_id)->first();

        // 作成した共有設定のmemo_idが、取得した共有設定のmemo_idと、一致するか確認
        $this->assertEquals($shareSetting->pluck('memo_id'), $sharedInfo->pluck('memo_id'));
    }
}
