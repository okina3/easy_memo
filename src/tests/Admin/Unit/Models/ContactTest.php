<?php

namespace Tests\Admin\Unit\Models;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Admin\TestCase;

class ContactTest extends TestCase
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
     * 問い合わせを作成するヘルパーメソッド
     * @param int $count 問い合わせの作成数
     * @return Collection 作成された問い合わせのコレクション
     */
    private function createContacts(int $count): Collection
    {
        // 指定された数の問い合わせを作成する
        return Contact::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    /**
     * 基本的なリレーションが、正しく機能しているかのテスト
     */
    public function testUserRelation()
    {
        // 1件の問い合わせを作成
        $contact = $this->createContacts(1)->first();

        // 問い合わせとユーザーのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $contact->user());
        // 自分のユーザーのIDが、作成した問い合わせに紐づいたユーザーのIDと、一致しているかを確認
        $this->assertEquals($this->user->id, $contact->user->id);
    }

    /**
     * 全ての問い合わせを、取得するスコープのテスト
     */
    public function testAvailableAllContactsScope()
    {
        // 3件の問い合わせを作成
        $contacts = $this->createContacts(3);
        // 全ての問い合わせを取得
        $allContacts = Contact::availableAllContacts()->get();

        // 作成した問い合わせのIDの配列が、取得した問い合わせのIDの配列と、一致するか確認
        $this->assertEquals($contacts->pluck('id')->toArray(), $allContacts->pluck('id')->toArray());
    }

    /**
     * 選択した問い合わせが、正しく取得するスコープのテスト
     */
    public function testAvailableSelectContactScope()
    {
        // 1件の問い合わせを作成
        $contact = $this->createContacts(1)->first();
        // 選択した問い合わせを取得
        $selectedContact = Contact::availableSelectContact($contact->id)->first();

        // 作成した問い合わせのIDが、取得した問い合わせのIDと、一致するか確認
        $this->assertEquals($contact->id, $selectedContact->id);
    }

    /**
     * 問い合わせをDBに、保存するスコープのテスト
     */
    public function testAvailableCreateContactScope()
    {
        // 1件の問い合わせのデータを作成
        $requestData = new Request([
            'subject' => 'テスト件名',
            'message' => 'テストメッセージ',
            'user_id' => $this->user->id,
        ]);

        // 問い合わせを保存
        Contact::availableCreateContact($requestData);

        // 作成された問い合わせがDBに存在するかを確認
        $this->assertDatabaseHas('contacts', [
            'subject' => 'テスト件名',
            'message' => 'テストメッセージ',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * 問い合わせを、検索するスコープのテスト
     */
    public function testSearchKeywordScope()
    {
        // 1件の問い合わせのデータを作成
        Contact::factory()->create([
            'subject' => '緊急: テスト問題',
            'message' => 'できるだけ早くこの問題を解決してください。',
            'user_id' => $this->user->id,
        ]);

        // キーワード「緊急」で、問い合わせを検索
        $searchResults = Contact::searchKeyword('緊急')->get();

        // 検索結果の最初の要素の件名に「緊急」が含まれているかを確認
        $this->assertStringContainsString('緊急', $searchResults->first()->subject);
    }
}
