<?php

namespace Tests\Admin\Feature;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

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
        // 管理者ユーザーを作成
        $admin = Admin::factory()->create();
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 管理者ユーザーを認証
        $this->actingAs($admin, 'admin');
    }

    /**
     * 問い合わせを作成するヘルパーメソッド
     * @param int $count 問い合わせの作成数
     * @return Collection 作成された問い合わせのコレクション
     */
    private function createContacts(int $count): Collection
    {
        // 指定された数の問い合わせを、現在のユーザーに関連付けて作成する
        return Contact::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    /**
     * 全ての問い合わせの一覧表示が、正しく行われることをテスト
     * @return void
     */
    public function testAllIndexContactController()
    {
        // 3件の問い合わせを作成
        $contacts = $this->createContacts(3);

        // 全ての問い合わせを表示する為に、リクエストを送信
        $response = $this->get(route('admin.contact.index'));

        // レスポンスが 'admin.contacts.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.contacts.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_contact', function ($viewContacts) use ($contacts) {
            // ビューに渡される問い合わせが、3件であり、かつ、問い合わせのID配列も、一致することを確認
            return $viewContacts->count() === 3 &&
                $viewContacts->pluck('id')->toArray() === $contacts->pluck('id')->toArray();
        });
    }

    /**
     * 絞り込んだ問い合わせの一覧表示が、正しく行われることをテスト
     * @return void
     */
    public function testSearchIndexContactController()
    {
        // 3件の問い合わせを作成
        $contacts = $this->createContacts(3);

        // 最初の問い合わせの件名をキーワードとして設定
        $keyword = $contacts->first()->subject;

        // 絞り込んだ問い合わせを一覧表示する為に、リクエストを送信
        $response = $this->get(route('admin.contact.index', ['keyword' => $keyword]));

        // レスポンスが 'admin.contacts.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.contacts.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_contact', function ($viewContacts) use ($contacts, $keyword) {
            // キーワードで、件名と内容から、問い合わせを絞り込み
            $filteredContacts = $contacts->filter(function ($contacts) use ($keyword) {
                return stripos($contacts->subject, $keyword) !== false || stripos($contacts->message, $keyword) !== false;
            });
            // ビューに渡される問い合わせと、絞り込まれた問い合わせの数が同じ、かつ、ID配列も一致することを確認
            return $viewContacts->count() === $filteredContacts->count() &&
                $viewContacts->pluck('id')->toArray() === $filteredContacts->pluck('id')->toArray();
        });
    }

    /**
     * 問い合わせの詳細表示が、正しく行われることをテスト
     * @return void
     */
    public function testShowContactController()
    {
        // 1件の問い合わせを作成
        $contact = $this->createContacts(1)->first();

        // 問い合わせの詳細画面を表示する為に、リクエストを送信
        $response = $this->get(route('admin.contact.show', $contact->id));

        // レスポンスが 'admin.contacts.show' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.contacts.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_contact', function ($viewContact) use ($contact) {
            // ビューに渡される問い合わせデータのIDが、作成した問い合わせデータのIDと一致することを確認
            return $viewContact->id === $contact->id;
        });
    }

    /**
     * 問い合わせが、正しく削除（ソフトデリート）されることをテスト
     * @return void
     */
    public function testDestroyContactController()
    {
        // 1件の問い合わせを作成
        $contact = $this->createContacts(1)->first();

        // 問い合わせを削除する為に、リクエストを送信
        $response = $this->delete(route('admin.contact.destroy', $contact->id), ['contentId' => $contact->id]);

        // 問い合わせがソフトデリートされたことを確認
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);

        // レスポンスが 'admin.contact.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.contact.index'));
        $response->assertSessionHas(['message' => 'ユーザーの問い合わせをゴミ箱に移動しました。', 'status' => 'alert']);
    }
}
