<?php

namespace Tests\Admin\Unit\Models;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\Admin\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Contact $contact;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー、問い合わせを作成
        $this->user = User::factory()->create();
        $this->contact = Contact::factory()->create(['user_id' => $this->user->id]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);
    }

    /**
     * Contactモデルの基本的なリレーションが正しく機能しているかのテスト
     */
    public function testUserRelation()
    {
        // 問い合わせに関連付けられたユーザーが、正しいかを確認
        $this->assertInstanceOf(User::class, $this->contact->user);
        // 問い合わせに関連付けられたユーザーのIDが、正しいかを確認
        $this->assertEquals($this->user->id, $this->contact->user->id);
    }

    /**
     * 問い合わせを、新しい順に取得するスコープのテスト
     */
    public function testAvailableAllContactsScope()
    {
        // 既存の問い合わせを削除
        Contact::query()->delete();

        // 2件の問い合わせを異なる作成日時で作成
        Contact::factory()->create(['message' => '問い合わせ 1', 'created_at' => now()->subSeconds(10)]);
        Contact::factory()->create(['message' => '問い合わせ 2', 'created_at' => now()]);

        // 問い合わせを作成順に取得
        $contacts = Contact::availableAllContacts()->get();

        // 最初の問い合わせが、最新の作成問い合わせであることを確認
        $this->assertEquals('問い合わせ 2', $contacts->first()->message);
        // 最後の問い合わせが、最も古い作成問い合わせであることを確認
        $this->assertEquals('問い合わせ 1', $contacts->last()->message);
    }

    /**
     * 選択した問い合わせが正しく取得するスコープのテスト
     */
    public function testAvailableSelectContactScope()
    {
        $selectedContact = Contact::availableSelectContact($this->contact->id)->first();

        // 取得した問い合わせのIDが、テスト用問い合わせのIDと一致するか確認
        $this->assertEquals($this->contact->id, $selectedContact->id);
    }

    /**
     * 問い合わせをDBに保存するスコープのテスト
     */
    public function testAvailableCreateContactScope()
    {
        // 問い合わせのデータを用意
        $data = new Request(['subject' => 'テスト件名', 'message' => 'テストメッセージ']);

        // スコープを使用して問い合わせを作成
        Contact::availableCreateContact($data);

        // 作成された問い合わせがDBに存在するかを確認
        $this->assertDatabaseHas('contacts', [
            'subject' => 'テスト件名',
            'message' => 'テストメッセージ',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * 問い合わせを検索するスコープのテスト
     */
    public function testSearchKeywordScope()
    {
        // テスト用の問い合わせを作成
        Contact::factory()->create(['subject' => '緊急: テスト問題', 'message' => 'できるだけ早くこの問題を解決してください。']);

        // キーワードで問い合わせを検索
        $searchResults = Contact::searchKeyword('緊急')->get();

        // 検索結果が1件であることを確認
        $this->assertCount(1, $searchResults);
        // 検索結果の問い合わせが期待通りであることを確認
        $this->assertStringContainsString('緊急', $searchResults->first()->subject);
    }
}
