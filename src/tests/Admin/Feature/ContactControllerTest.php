<?php

namespace Tests\Admin\Feature;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // 管理者ユーザーを作成
        $this->admin = Admin::factory()->create();
        // ユーザーを認証
        $this->actingAs($this->admin, 'admin');
    }

    /**
     * 問い合わせの一覧表示が正しく行われることをテスト
     * @return void
     */
    public function testIndexContactController()
    {
        // ユーザーと問い合わせを作成
        $user = User::factory()->create();
        $contacts = Contact::factory()->count(5)->create(['user_id' => $user->id]);

        // キーワード検索のためのリクエストを作成
        $keyword = $contacts->first()->subject;
        $response = $this->get(route('admin.contact.index', ['keyword' => $keyword]));

        // レスポンスが正しいビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.contacts.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_contact', function ($viewContacts) use ($contacts, $keyword) {
            // ビューから取得した問い合わせをコレクションに変換
            $viewContacts = collect($viewContacts);

            // キーワードでフィルタリングされた結果を検証
            $filteredContacts = $contacts->filter(function ($contact) use ($keyword) {
                return stripos($contact->subject, $keyword) !== false || stripos($contact->message, $keyword) !== false;
            });

            // 問い合わせの数が期待通りかを確認。問い合わせのIDが期待通りかを確認
            return $viewContacts->count() === $filteredContacts->count() &&
                $viewContacts->pluck('id')->sort()->values()->all() === $filteredContacts->pluck('id')->sort()->values()->all();
        });
    }

    /**
     * 問い合わせの詳細表示が正しく行われることをテスト
     * @return void
     */
    public function testShowContactController()
    {
        // ユーザーと問い合わせを作成
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        // 問い合わせ詳細表示のリクエストを送信
        $response = $this->get(route('admin.contact.show', $contact->id));

        // レスポンスが正しいビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.contacts.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_contact', function ($viewContact) use ($contact) {
            return $viewContact->id === $contact->id;
        });
    }

    /**
     * 問い合わせが正しく削除（ソフトデリート）されることをテスト
     * @return void
     */
    public function testDestroyContactController()
    {
        // ユーザーと問い合わせを作成
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        // 問い合わせ削除のリクエストデータを作成
        $requestData = ['contentId' => $contact->id];

        // 問い合わせ削除のリクエストを送信
        $response = $this->delete(route('admin.contact.destroy', $contact->id), $requestData);

        // 問い合わせがソフトデリートされたことを確認
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.contact.index'));
        $response->assertSessionHas(['message' => 'ユーザーの問い合わせをゴミ箱に移動しました。', 'status' => 'alert']);
    }
}
