<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\User;
use App\Notifications\User\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\User\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * テスト前の初期設定
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー、メモを作成
        $this->user = User::factory()->create();
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * ユーザーパスワードリセット通知のテスト
     * @return void
     */
    public function testSendPasswordResetNotification()
    {
        // 通知を偽装する
        Notification::fake();

        // ダミートークンを設定
        $token = 'dummy-token123';
        // パスワードリセット通知をユーザーに送信
        $this->user->sendPasswordResetNotification($token);

        // 指定されたユーザーに対してResetPasswordNotificationが送信されたかを確認
        Notification::assertSentTo($this->user,
            ResetPasswordNotification::class, fn($notification) => $notification->token === $token);
    }

    /**
     * パスワードのハッシュ処理が正しく機能しているかのテスト
     * @return void
     */
    public function testPasswordIsHashed()
    {
        // テストユーザーを作成
        $user = User::create(['name' => 'テストユーザー', 'email' => 'test@example.com', 'password' => 'plainpassword',]);

        // パスワードがハッシュされていることを確認
        $this->assertTrue(Hash::check('plainpassword', $user->password));
        // ハッシュされているため、元のパスワードと一致しないことを確認
        $this->assertNotEquals('plainpassword', $user->password);
    }

    /**
     * Userモデルの基本的なリレーションが正しく機能しているかのテスト
     * @return void
     */
    public function testUserMemoRelationship()
    {
        // ユーザーに関連付けられたメモのインスタンスが正しいかを確認
        $this->assertInstanceOf(Memo::class, $this->user->memos->first());
        // ユーザーに関連付けられたメモのIDが、正しいかを確認
        $this->assertEquals($this->memo->id, $this->user->memos->first()->id);
    }

    /**
     * ユーザーを更新順に取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllUsersScope()
    {
        // 既存のユーザーを削除
        User::query()->delete();

        // 2人のユーザーを異なる更新日時で作成
        User::factory()->create(['email' => 'user1@example.com', 'updated_at' => now()->subSeconds(10)]);
        User::factory()->create(['email' => 'user2@example.com', 'updated_at' => now()]);

        // ユーザーを更新順に取得
        $users = User::availableAllUsers()->get();

        // 最初のユーザーが、最新の更新ユーザーであることを確認
        $this->assertEquals('user2@example.com', $users->first()->email);
        // 最後のユーザーが、最も古い更新ユーザーであることを確認
        $this->assertEquals('user1@example.com', $users->last()->email);
    }

    /**
     * 選択したユーザーを取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectUserScope()
    {
        // 選択したユーザーを取得
        $selectedUser = User::availableSelectUser($this->user->id)->first();

        // 取得したユーザーが期待通りであることを確認
        $this->assertEquals($this->user->id, $selectedUser->id);
    }

    /**
     * 選択したメールアドレスからユーザーを取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectMailUserScope()
    {
        // ユニークなメールアドレスのユーザーを作成
        $user = User::factory()->create(['email' => 'unique@example.com']);

        // メールアドレスでユーザーを取得
        $selectedMailUser = User::availableSelectMailUser($user->email)->first();

        // 取得したユーザーが期待通りであることを確認
        $this->assertEquals($user->email, $selectedMailUser->email);
    }

    /**
     * メールアドレスでユーザーを検索するスコープのテスト
     * @return void
     */
    public function testSearchKeywordScope()
    {
        // 特定のメールアドレスを持つユーザーを作成
        User::factory()->create(['email' => 'user123@example.com']);

        // キーワードでユーザーを検索
        $searchResults = User::searchKeyword('user123')->get();

        // 検索結果が1件であることを確認
        $this->assertCount(1, $searchResults);
        // 検索結果のユーザーのメールアドレスが期待通りであることを確認
        $this->assertEquals('user123@example.com', $searchResults->first()->email);
    }
}
