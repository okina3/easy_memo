<?php

namespace Tests\Admin\Feature;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\Admin\TestCase;

class TrashedContactControllerTest extends TestCase
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
        $this->admin = Admin::factory()->create();
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 管理者ユーザーを認証
        $this->actingAs($this->admin, 'admin');
    }

    /**
     * ソフトデリートされた問い合わせを作成するヘルパーメソッド
     * @param int $count 問い合わせの作成数
     * @return Collection 作成された問い合わせのコレクション
     */
    private function createTrashedContacts(int $count): Collection
    {
        // 指定された数のソフトデリートされた問い合わせを、現在のユーザーに関連付けて作成する
        return Contact::factory()->count($count)->create(['user_id' => $this->user->id, 'deleted_at' => now()]);
    }

    /**
     * ソフトデリートした問い合わせ一覧が正しく表示されることをテスト
     * @return void
     */
    public function testIndexTrashedContactController()
    {
        // ソフトデリートされた問い合わせを作成
        $contacts = $this->createTrashedContacts(3);

        // indexメソッドを呼び出して、レスポンスを確認
        $response = $this->get(route('admin.trashed-contact.index'));

        // レスポンスが 'admin.trashedContacts.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('admin.trashedContacts.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('all_trashed_contacts', function ($viewContacts) use ($contacts) {
            return $viewContacts->count() === 3 && $viewContacts->first()->id === $contacts->first()->id;
        });
    }

    /**
     * ソフトデリートした問い合わせが正しく元に戻されることをテスト
     * @return void
     */
    public function testUndoTrashedContactController()
    {
        // ソフトデリートされた問い合わせを作成
        $contact = $this->createTrashedContacts(1)->first();

        // undoメソッドを呼び出してレスポンスを確認
        $response = $this->patch(route('admin.trashed-contact.undo'), ['contentId' => $contact->id]);

        // 問い合わせが元に戻されたことを確認
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'deleted_at' => null,
        ]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.trashed-contact.index'));
        $response->assertSessionHas(['message' => 'ユーザーの問い合わせを、元に戻しました。', 'status' => 'info']);
    }

    /**
     * ソフトデリートした問い合わせが正しく完全削除されることをテスト
     * @return void
     */
    public function testDestroyTrashedContactController()
    {
        // ソフトデリートされた問い合わせを作成
        $contact = $this->createTrashedContacts(1)->first();

        // destroyメソッドを呼び出してレスポンスを確認
        $response = $this->delete(route('admin.trashed-contact.destroy'), ['contentId' => $contact->id]);

        // 問い合わせが完全に削除されたことを確認
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);

        // レスポンスが正しいリダイレクト先を指していることを確認
        $response->assertRedirect(route('admin.trashed-contact.index'));
        $response->assertSessionHas(['message' => 'ユーザーの問い合わせを、完全に削除しました。', 'status' => 'alert']);
    }
}
