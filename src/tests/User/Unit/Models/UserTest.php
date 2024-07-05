<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\User;
use App\Notifications\User\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\User\TestCase;

class UserTest extends TestCase
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
     * メモを作成するヘルパーメソッド
     * @param int $count メモの作成数
     * @return Collection 作成されたメモのコレクション
     */
    private function createMemos(int $count): Collection
    {
        // 指定された数のメモを、現在のユーザーに関連付けて作成する
        return Memo::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    /**
     * ユーザーを作成するヘルパーメソッド
     * @param int $count ユーザーの作成数
     * @return Collection 作成されたユーザーのコレクション
     */
    private function createUsers(int $count): Collection
    {
        // 指定された数のユーザーを作成する
        return User::factory()->count($count)->create();
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
     * 基本的なリレーションが、正しく機能しているかのテスト
     * @return void
     */
    public function testUserMemoRelationship()
    {
        // 2件のメモを作成
        $attachedMemos = $this->createMemos(2);

        // ユーザーとメモのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(HasMany::class, $this->user->memos());
        // 作成したメモのIDが、自分のユーザーに紐づいたメモのIDと、一致しているかを確認
        $this->assertEquals($attachedMemos->pluck('id')->toArray(), $this->user->memos->pluck('id')->toArray());
    }

    /**
     * ユーザーを更新順に、取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllUsersScope()
    {
        // 既存のユーザーを削除
        User::query()->delete();

        // 3人のユーザーを異なる更新日時で作成
        User::factory()->create(['email' => 'user1@example.com', 'updated_at' => now()]);
        User::factory()->create(['email' => 'user2@example.com', 'updated_at' => now()->addSeconds(5)]);
        User::factory()->create(['email' => 'user3@example.com', 'updated_at' => now()->addSeconds(10)]);

        // ユーザーを更新順に取得
        $users = User::availableAllUsers()->get();

        // 最後に作成したユーザーが、最新の更新ユーザーであることを確認
        $this->assertEquals('user3@example.com', $users->first()->email);
        // 最初に作成したユーザーが、最も古い更新ユーザーであることを確認
        $this->assertEquals('user1@example.com', $users->last()->email);
    }

    /**
     * 選択したユーザーを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectUserScope()
    {
        // 1件のユーザーを作成
        $user = $this->createUsers(1)->first();
        // 選択したユーザーを取得
        $selectedUser = User::availableSelectUser($user->id)->first();

        // 作成したユーザーのIDが、取得したユーザーのIDと、一致するか確認
        $this->assertEquals($user->id, $selectedUser->id);
    }

    /**
     * 選択したメールアドレスからユーザーを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectMailUserScope()
    {
        // 1件のユーザーを作成
        $user = $this->createUsers(1)->first();
        // メールアドレスでユーザーを取得
        $selectedMailUser = User::availableSelectMailUser($user->email)->first();

        // 作成したユーザーのメールアドレスが、取得したユーザーのメールアドレスと、一致するか確認
        $this->assertEquals($user->email, $selectedMailUser->email);
    }

    /**
     * メールアドレスでユーザーを、検索するスコープのテスト
     * @return void
     */
    public function testSearchKeywordScope()
    {
        // 1件のユーザーを作成
        $user = $this->createUsers(1)->first();

        // キーワードでユーザーを検索
        $searchResult = User::searchKeyword($user->email)->get();

        // 検索結果が1件であることを確認
        $this->assertCount(1, $searchResult);
        // 作成したユーザーのメールアドレスが、検索結果のユーザーのメールアドレスと、一致するか確認
        $this->assertEquals($user->email, $searchResult->first()->email);
    }
}
