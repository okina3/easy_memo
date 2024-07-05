<?php

namespace Tests\User\Unit\Models;

use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class MemoTest extends TestCase
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
     * ソフトデリートされたメモを作成するヘルパーメソッド
     * @param int $count メモの作成数
     * @return Collection ソフトデリートされたメモのコレクション
     */
    private function createDeletedMemos(int $count): Collection
    {
        // 指定された数のメモを、現在のユーザーに関連付けて作成し、deleted_atを設定する
        return Memo::factory()->count($count)->create(['user_id' => $this->user->id, 'deleted_at' => now(),]);
    }

    /**
     * メモにタグと画像を関連付けるヘルパーメソッド
     * @param Memo $memo 関連付けるメモ
     * @param int $tagCount 作成するタグの数
     * @param int $imageCount 作成する画像の数
     * @return array タグと画像の配列
     */
    private function attachTagsAndImages(Memo $memo, int $tagCount, int $imageCount): array
    {
        // タグを作成し、メモに関連付け
        $tags = Tag::factory()->count($tagCount)->create();
        $memo->tags()->attach($tags);

        // 画像を作成し、メモに関連付け
        $images = Image::factory()->count($imageCount)->create();
        $memo->images()->attach($images);

        // タグと画像を返す
        return [$tags, $images];
    }

    /**
     * 基本的なリレーションが、正しく機能しているかのテスト
     * @return void
     */
    public function testMemoRelations()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // メモに2件のタグと2件の画像を関連付け
        [$attachedTags, $attachedImages] = $this->attachTagsAndImages($memo, 2, 2);

        // メモとタグのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $memo->tags());
        // 作成した関連付けられたタグのID配列が、作成したメモに紐づいたタグのID配列と、一致しているかを確認
        $this->assertEquals($attachedTags->pluck('id')->toArray(), $memo->tags->pluck('id')->toArray());

        // メモと画像のリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $memo->images());
        // 作成した関連付けられた画像のID配列が、作成したメモに紐づいた画像のID配列と、一致しているかを確認
        $this->assertEquals($attachedImages->pluck('id')->toArray(), $memo->images->pluck('id')->toArray());

        // 共有設定を作成し、メモに関連付け
        $shareSetting = ShareSetting::factory()->create(['memo_id' => $memo->id]);

        // メモと共有設定のリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(HasMany::class, $memo->shareSettings());
        // 作成した共有設定のIDの配列が、メモに紐づいた共有設定のIDの配列と、一致しているかを確認
        $this->assertEquals($shareSetting->pluck('id')->toArray(), $memo->shareSettings->pluck('id')->toArray());

        // メモとユーザーのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $memo->user());
        // 自分のユーザーのIDが、作成したメモに紐づいたユーザーのIDと、一致しているかを確認
        $this->assertEquals($this->user->id, $memo->user->id);
    }

    /**
     * 自分自身の全てのメモを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllMemosScope()
    {
        // 3件のメモを作成
        $memos = $this->createMemos(3);
        // 全てのメモを取得
        $allMemos = Memo::availableAllMemos()->get();

        // 作成したメモのIDの配列が、取得したメモのIDの配列と、一致するか確認
        $this->assertEquals($memos->pluck('id')->toArray(), $allMemos->pluck('id')->toArray());
    }

    /**
     * 自分自身の選択したメモを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectMemoScope()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // 選択したメモを取得
        $selectedMemo = Memo::availableSelectMemo($memo->id)->first();

        // 作成したメモのIDが、取得したメモのIDと、一致するか確認
        $this->assertEquals($memo->id, $selectedMemo->id);
    }

    /**
     * 自分自身の全ての削除済みのメモを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllTrashedMemosScope()
    {
        // 3件のソフトデリートしたメモを作成
        $memos = $this->createDeletedMemos(3);
        // 全てのソフトデリートしたメモを取得
        $trashedMemos = Memo::availableAllTrashedMemos()->get();

        // 作成した削除済みメモのIDの配列が、取得した削除済みメモのIDの配列と、一致するか確認
        $this->assertEquals($memos->pluck('id')->toArray(), $trashedMemos->pluck('id')->toArray());
    }

    /**
     * 自分自身の選択した削除済みのメモを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectTrashedMemoScope()
    {
        // 1件のソフトデリートしたメモを作成
        $memo = $this->createDeletedMemos(1)->first();
        // 選択した削除済みのメモを取得
        $selectedTrashedMemo = Memo::availableSelectTrashedMemo($memo->id)->first();

        // 作成した削除済みメモのIDが、取得した削除済みメモのIDと、一致するか確認
        $this->assertEquals($memo->id, $selectedTrashedMemo->id);
    }
}
