<?php

namespace Tests\User\Feature\Controllers;

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
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * 管理人への問い合わせの新規作成画面が、正しく表示されることをテスト
     * @return void
     */
    public function testCreateContactController()
    {
        // 管理人への問い合わせの新規作成画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.contact.create'));

        // レスポンスが 'user.contacts.create' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.contacts.create');

        // ブラウザバック対策用のセッションが設定されていることを確認
        $this->assertTrue(Session::has('back_button_clicked'));
    }

    /**
     * 管理人への問い合わせが、正しく保存されることをテスト
     * @return void
     */
    public function testStoreContactController()
    {
        // 保存するデータを作成
        $requestData = [
            'subject' => 'テスト、問い合わせ',
            'message' => 'これはテストメッセージです。',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // 管理人への問い合わせを保存する為に、リクエストを送信
        $response = $this->post(route('user.contact.store'), $requestData);

        // 問い合わせが保存されたことを確認
        $this->assertDatabaseHas('contacts', [
            'subject' => 'テスト、問い合わせ', 'message' => 'これはテストメッセージです。', 'user_id' => $this->user->id,
        ]);

        // レスポンスが 'index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => '管理人にメッセージを送りました。', 'status' => 'info']);
    }
}
