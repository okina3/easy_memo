<?php

namespace Tests\User\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\User\TestCase;

class ContactControllerTest extends TestCase
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
        // ログインユーザーを作成し、プロパティに格納
        $this->user = $this->createUserWithAuthenticatedSession();
    }

    /**
     * ログインユーザーを作成し認証済みセッションを開始するヘルパーメソッド
     * @return User 認証済みのユーザーオブジェクト
     */
    private function createUserWithAuthenticatedSession(): User
    {
        // ユーザーを作成
        $user = User::factory()->create();
        // ユーザーを認証
        $this->actingAs($user);
        // 認証済みのユーザーを返す
        return $user;
    }

    /**
     * createメソッドが正しく動作することをテスト
     * @return void
     */
    public function testCreateContactController()
    {
        // createメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('user.contact.create'));

        // レスポンスが 'user.contacts.create' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.contacts.create');

        // ブラウザバック対策用のセッションが設定されていることを確認
        $this->assertTrue(Session::has('back_button_clicked'));
    }

    /**
     * storeメソッドが正しく動作することをテスト
     * @return void
     */
    public function testStoreContactController()
    {
        // リクエストデータを作成
        $requestData = [
            'subject' => 'テスト、問い合わせ',
            'message' => 'これはテストメッセージです。',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(env('BROWSER_BACK_KEY')));

        // storeメソッドを呼び出してレスポンスを確認
        $response = $this->post(route('user.contact.store'), $requestData);

        // 問い合わせが保存されたことを確認
        $this->assertDatabaseHas('contacts', [
            'subject' => 'テスト、問い合わせ',
            'message' => 'これはテストメッセージです。',
            'user_id' => $this->user->id,
        ]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => '管理人にメッセージを送りました。', 'status' => 'info']);
    }
}
