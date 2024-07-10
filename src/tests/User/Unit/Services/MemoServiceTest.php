<?php

namespace Tests\User\Unit\Services;

use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use App\Services\MemoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class MemoServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $secondaryUser;

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
        // 2人目の別のユーザーを作成
        $this->secondaryUser = User::factory()->create();

        // 認証済みのユーザーを返す
        $this->actingAs($this->user);
    }

    /**
     * メモを作成するヘルパーメソッド
     * @param User $user 作成するメモに関連付けるユーザー
     * @return Memo 作成されたメモのインスタンス
     */
    private function createMemo(User $user): Memo
    {
        // メモを、現在のユーザーに関連付けて作成する
        return Memo::factory()->create(['user_id' => $user->id]);
    }

    /**
     * タグを作成するヘルパーメソッド
     * @param User $user 作成するタグに関連付けるユーザー
     * @return Tag 作成されたタグのインスタンス
     */
    private function createTag(User $user): Tag
    {
        // タグを、現在のユーザーに関連付けて作成する
        return Tag::factory()->create(['user_id' => $user->id]);
    }

    /**
     * 画像を作成するヘルパーメソッド
     * @param User $user 作成する画像に関連付けるユーザー
     * @return Image 作成された画像のインスタンス
     */
    private function createImage(User $user): Image
    {
        // 画像を、現在のユーザーに関連付けて作成する
        return Image::factory()->create(['user_id' => $user->id]);
    }

    /**
     * メモを共有させる設定を作成するヘルパーメソッド
     * @param User $sharingUser 共有設定させたいユーザー
     * @param Memo $memo 共有するメモ
     * @return ShareSetting 作成された共有設定のインスタンス
     */
    private function createShareSetting(User $sharingUser, Memo $memo): ShareSetting
    {
        return ShareSetting::factory()->create([
            // 共有させたいユーザー
            'sharing_user_id' => $sharingUser->id,
            // メモの選択
            'memo_id' => $memo->id,
            // 編集も可能
            'edit_access' => true
        ]);
    }

    /**
     * 別のユーザーのメモを見られなくするメソッドのテスト
     * @return void
     */
    public function testCheckUserMemo()
    {
        // 2人目の別のユーザーを作成
        $secondaryUser = User::factory()->create();
        // 別のユーザーに関連するメモを作成
        $secondaryUserMemo = $this->createMemo($secondaryUser);

        // リクエストを作成
        $request = Request::create('/memos/' . $secondaryUserMemo->id);
        // リクエストのルートを設定
        $request->setRouteResolver(function () use ($request) {
            // 新しいルートオブジェクトを作成し、リクエストにバインド
            return (new Route('GET', '/memos/{memo}', []))->bind($request);
        });

        // 異常なユーザーのメモアクセスは、例外の発生を期待（404エラー）
        $this->expectException(NotFoundHttpException::class);
        // メモの所有者を確認するサービスメソッドを実行
        MemoService::checkUserMemo($request);
    }

    /**
     * 全メモ、また、検索したメモを一覧表示するメソッドのテスト
     * @return void
     */
    public function testSearchMemos()
    {
        // 4件の自分のメモを作成
        Memo::factory()->count(4)->create(['user_id' => $this->user->id]);
        // 1件の自分のメモを作成
        $memo = $this->createMemo($this->user);
        // 1件の自分のタグを作成
        $tag = $this->createTag($this->user);
        // 1件の共有設定を作成（自分のメモを、2人目のユーザーに共有）
        $this->createShareSetting($this->secondaryUser, $memo);

        // メモにタグを関連付ける
        $memo->tags()->attach($tag);
        // タグのIDをパラメーターに追加
        request()->merge(['tag' => $tag->id]);
        // メモを検索するサービスメソッドを実行
        $response = MemoService::searchMemos();

        // 期待されるメモの数が、1であることを確認
        $this->assertCount(1, $response);

        // 共有設定を確認
        $sharedMemo = $response->firstWhere('id', $memo->id);
        $this->assertNotNull($sharedMemo, 'Shared memo not found in the response');
        $this->assertEquals('共有中', $sharedMemo->status ?? null);

        // タグのIDをパラメーターから削除
        request()->replace([]);
        // 再度メモを検索するサービスメソッドを実行（タグなし）
        $response = MemoService::searchMemos();

        // 期待されるメモの数が、5であることを確認
        $this->assertCount(5, $response);
    }

    /**
     * タグが空の場合と画像が空の場合のテスト
     * @return void
     */
    public function testAttachTagsAndImages()
    {
        // タグが空の場合
        // 1件の自分のメモを作成
        $memo = $this->createMemo($this->user);
        // 1件の自分の画像を作成
        $image = $this->createImage($this->user);
        // タグが空、画像が指定されたリクエストを作成
        $requestWithNoTags = new Request(['tags' => [], 'images' => [$image->id]]);
        // メモにタグと画像を関連付けるサービスメソッドを実行
        MemoService::attachTagsAndImages($requestWithNoTags, $memo->id);

        // タグが、関連付けられていないことを確認
        $this->assertDatabaseMissing('memo_tags', ['memo_id' => $memo->id]);
        // 画像が、関連付けられていることを確認
        $this->assertDatabaseHas('memo_images', ['image_id' => $image->id, 'memo_id' => $memo->id]);

        // 画像が空の場合
        // 1件の自分のメモを作成
        $memo = $this->createMemo($this->user);
        // 1件の自分のタグを作成
        $tag = $this->createTag($this->user);
        // 画像が空、タグが指定されたリクエストを作成
        $requestWithNoImages = new Request(['tags' => [$tag->id], 'images' => []]);
        // メモにタグと画像を関連付けるサービスメソッドを実行
        MemoService::attachTagsAndImages($requestWithNoImages, $memo->id);

        // 画像が、関連付けられていないことを確認
        $this->assertDatabaseMissing('memo_images', ['memo_id' => $memo->id]);
        // タグが、関連付けられていることを確認
        $this->assertDatabaseHas('memo_tags', ['tag_id' => $tag->id, 'memo_id' => $memo->id]);
    }

    /**
     * メモ更新機能のテスト
     * @return void
     */
    public function testUpdateMemo()
    {
        // 1件の自分のメモを作成
        $memo = $this->createMemo($this->user);

        // メモ更新のためのリクエストを作成
        $request = new Request([
            'memoId' => $memo->id,
            'title' => '更新されたタイトル',
            'content' => '更新された内容'
        ]);
        // メモを更新のサービスメソッドを実行
        $updatedMemo = MemoService::updateMemo($request);

        // メモのタイトルと内容が更新されていることを確認
        $this->assertEquals('更新されたタイトル', $updatedMemo->title);
        $this->assertEquals('更新された内容', $updatedMemo->content);
    }

    /**
     * メモ共有状態チェック機能のテスト
     * @return void
     */
    public function testCheckShared()
    {
        // 1件の自分のメモを作成
        $memo = $this->createMemo($this->user);
        // 共有設定を作成（自分のメモを、2人目のユーザーに共有）
        $this->createShareSetting($this->secondaryUser, $memo);
        // メモの共有状態をチェックのサービスメソッドを実行
        $checkedMemo = MemoService::checkShared($memo);

        // メモのステータスが「共有中」となっていることを確認
        $this->assertEquals('共有中', $checkedMemo->status);
    }
}