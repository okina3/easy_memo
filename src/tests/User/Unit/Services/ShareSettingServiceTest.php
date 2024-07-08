<?php

namespace Tests\User\Unit\Services;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use App\Services\ShareSettingService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class ShareSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
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
     * 同じメモを複数人に共有させる設定を作成するヘルパーメソッド
     * @param Memo $memo 共有するメモ
     * @param User $sharingUser 共有させたいユーザー
     * @return ShareSetting 共有設定
     */
    private function createSetting(Memo $memo, User $sharingUser): ShareSetting
    {
        return ShareSetting::factory()->create([
            // 共有させたいユーザー
            'sharing_user_id' => $sharingUser->id,
            // メモの選択
            'memo_id' => $memo->id,
        ]);
    }

    /**
     *  全ての共有メモ、ユーザー別の共有メモを、切り分けて表示するメソッドのテスト
     */
    public function testAllSearchSharedMemos()
    {
        // 4件の他人のメモを、自分に共有する設定を作成
        $shareSettings = new Collection([
            $this->createShareSetting($this->secondaryUser, $this->user),
            $this->createShareSetting($this->tertiaryUser, $this->user),
            $this->createShareSetting($this->secondaryUser, $this->user),
            $this->createShareSetting($this->tertiaryUser, $this->user)
        ]);

        // ユーザーを指定して、共有メモを検索する（ユーザーのIDをパラメーターに追加）
        request()->merge(['user' => encrypt($this->tertiaryUser->id)]);
        // 共有メモを検索するサービスメソッドを実行
        $result = ShareSettingService::searchSharedMemos($shareSettings);

        // サービスメソッドから取得した情報をコレクションに変換
        $result = collect($result);
        // 取得したメモが、特定のユーザーからのものであることを確認
        $this->assertTrue($result->pluck('user_id')->every(function ($userId) {
            return $userId === $this->tertiaryUser->id;
        }));

        // ユーザーを指定しないで、全ての共有メモを検索する（ユーザーのIDをパラメーターから削除）
        request()->replace([]);
        // 共有メモを検索するサービスメソッドを実行
        $result = ShareSettingService::searchSharedMemos($shareSettings);

        // サービスメソッドから取得した情報をコレクションに変換
        $result = collect($result);
        // 作成した共有設定の、memo_id配列が、取得した共有メモのID配列と一致することを確認
        $this->assertEquals($shareSettings->pluck('memo_id')->toArray(), $result->pluck('id')->toArray());
    }

    /**
     * メモを共有しているユーザーを取得するテスト
     */
    public function testSearchSharedUsers()
    {
        // 3件の他人のメモを、自分に共有する設定を作成
        $shareSettings = new Collection([
            $this->createShareSetting($this->secondaryUser, $this->user),
            $this->createShareSetting($this->tertiaryUser, $this->user),
            $this->createShareSetting($this->secondaryUser, $this->user)
        ]);
        // 共有しているメモの共有設定を取得するサービスメソッドを実行
        $result = ShareSettingService::searchSharedUser($shareSettings);

        // サービスメソッドから取得した情報をコレクションに変換
        $result = collect($result);
        // 期待される共有ユーザーの数が2人であることを確認
        $this->assertCount(2, $result);
        // 取得した共有ユーザーの名前が、期待されるユーザー名と一致することを確認
        $this->assertEquals(
            [$this->secondaryUser->name, $this->tertiaryUser->name], $result->pluck('name')->toArray());
    }

    /**
     * 共有設定が、重複していたら解除するテスト
     */
    public function testResetDuplicateShareSettings()
    {
        // 1件の共有設定のデータを作成（2人目のユーザーのメモを、自分に共有）
        $shareSettings = $this->createShareSetting($this->secondaryUser, $this->user);
        // 重複した共有設定を削除するサービスメソッドを実行
        ShareSettingService::resetDuplicateShareSettings($shareSettings->memo_id, $this->user->id);

        // 共有設定が削除されたことを検証
        $this->assertEquals(0, ShareSetting::count());
    }

    /**
     * 共有されていないメモの詳細を見られなくするテスト
     */
    public function testCheckSharedMemoShow()
    {
        // 1件の共有設定のデータを作成（2人目のユーザーのメモを、自分に共有）
        $shareSettings = $this->createShareSetting($this->secondaryUser, $this->user);
        // 作成した共有設定を削除
        ShareSetting::where('memo_id', $shareSettings->memo_id)->delete();

        // 例外の発生を期待（404エラー）
        $this->expectException(NotFoundHttpException::class);
        // 共有メモの詳細を表示するサービスメソッドを実行
        ShareSettingService::checkSharedMemoShow($shareSettings->memo_id);
    }

    /**
     * 共有、許可されていないメモの編集をできなくするテスト
     */
    public function testCheckSharedMemoEdit()
    {
        // 1件の編集不可（false）の共有設定を作成（2人目のユーザーのメモを、自分に共有）
        $shareSettings = $this->createShareSetting($this->secondaryUser, $this->user, false);

        // 例外の発生を期待（404エラー）
        $this->expectException(NotFoundHttpException::class);
        // 共有メモを編集するサービスメソッドを実行
        ShareSettingService::checkSharedMemoEdit($shareSettings->memo_id);
    }

    /**
     * 自分が共有しているメモの共有状態の情報を、取得するテスト
     */
    public function testCheckSharedMemoStatus()
    {
        // 1件の共有設定のデータを作成（自分のメモを、2人目のユーザーに共有、編集不可）
        $shareSettings = $this->createShareSetting($this->user, $this->secondaryUser, false);
        // 共有メモの共有状態を取得するサービスメソッドを実行
        $sharedUsers = ShareSettingService::checkSharedMemoStatus($shareSettings->memo_id);

        // サービスメソッドから取得した情報をコレクションに変換
        $sharedUsers = collect($sharedUsers);
        // 取得した共有設定が、編集不可（false）であることを検証
        $this->assertEquals(0, $sharedUsers->pluck('access')->first());
    }

    /**
     * 選択したメモの全共有設定の削除のテスト。
     */
    public function testDeleteShareSettingAll()
    {
        // 1件の自分のメモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 自分のメモを、2人目のユーザーに共有
        $this->createSetting($memo, $this->secondaryUser);
        // 自分のメモを、3人目のユーザーに共有
        $this->createSetting($memo, $this->tertiaryUser);
        // 全共有設定を削除するサービスメソッドを実行
        ShareSettingService::deleteShareSettingAll($memo->id);

        // データベースから共有設定が削除されたことを検証
        $this->assertDatabaseMissing('share_settings', ['memo_id' => $memo->id]);
    }
}
