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
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class MemoServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private Tag $tag;
    private Image $image;
    private ShareSetting $shareSetting;
    private User $otherUser;
    private Memo $otherUserMemo;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // ２人のテスト用のユーザーを生成
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        // ２人のテスト用のメモを生成
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->otherUserMemo = Memo::factory()->create(['user_id' => $this->otherUser->id]);

        // userのタグ、画像、共有設定を作成
        $this->tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $this->image = Image::factory()->create(['user_id' => $this->user->id]);

        // userの共有設定の作成（userは、otherUserに、メモを共有）
        $this->shareSetting = ShareSetting::factory()->create([
            'sharing_user_id' => $this->otherUser->id,
            'memo_id' => $this->memo->id,
            'edit_access' => true
        ]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);
    }

    /**
     * checkUserMemoメソッドのテスト。
     * @return void
     */
    public function testCheckUserMemoWithOthersMemo()
    {
        // 特定のメモIDを持つリクエストを作成
        $request = Request::create('/memos/' . $this->memo->id);
        $request->setRouteResolver(function () use ($request) {
            return (new Route('GET', '/memos/{memo}', []))->bind($request, ['memo' => $this->memo->id]);
        });

        // 正常なユーザーの場合、例外は発生しない
        MemoService::checkUserMemo($request);

        // 異常なユーザーの場合、404エラーが発生することを確認
        $anotherUser = User::factory()->create();
        $anotherMemo = Memo::factory()->create(['user_id' => $anotherUser->id]);

        // 特定のメモIDを持つリクエストを作成（異常なユーザーの場合）
        $request = Request::create('/memos/' . $anotherMemo->id);
        $request->setRouteResolver(function () use ($request, $anotherMemo) {
            return (new Route('GET', '/memos/{memo}', []))->bind($request, ['memo' => $anotherMemo->id]);
        });

        // 異常なユーザーのメモアクセスは404エラーを発生させる
        $this->expectException(NotFoundHttpException::class);
        MemoService::checkUserMemo($request);
    }

    /**
     * タグが指定された場合、関連するメモが正しく取得され、
     * 共有設定が有効なメモのステータスが正しく更新されることを確認
     * @return void
     */
    public function testSearchMemosWithTagAndStatusUpdateWhenShared()
    {
        // リレーションを設定（メモにタグを関連付け）
        $this->memo->tags()->attach($this->tag);
        // タグをリクエストに追加
        request()->merge(['tag' => $this->tag->id]);
        // メモを検索
        $response = MemoService::searchMemos();

        // メモが取得されていることを確認
        $this->assertNotEmpty($response);
        $this->assertTrue($response->contains($this->memo));

        // 取得したメモのステータスを確認
        $sharedMemo = $response->firstWhere('id', $this->memo->id);
        $this->assertEquals('共有中', $sharedMemo->status ?? null);
    }

    /**
     * タグが空の場合と画像が空の場合のテスト
     * @return void
     */
    public function testAttachTagsAndImagesWithNoTagsAndNoImages()
    {
        // タグが空の場合のリクエスト
        $memoWithNoTags = Memo::factory()->create(['user_id' => $this->user->id]);
        $requestWithNoTags = new Request(['tags' => [], 'images' => [$this->image->id]]);
        // タグと画像の関連付け
        MemoService::attachTagsAndImages($requestWithNoTags, $memoWithNoTags->id);

        // タグが関連付けられていないことを確認
        $this->assertDatabaseMissing('memo_tags', ['memo_id' => $memoWithNoTags->id]);
        // 画像が関連付けられていることを確認
        $this->assertDatabaseHas('memo_images', ['image_id' => $this->image->id, 'memo_id' => $memoWithNoTags->id]);


        // 画像が空の場合のリクエスト
        $memoWithNoImages = Memo::factory()->create(['user_id' => $this->user->id]);
        $requestWithNoImages = new Request(['tags' => [$this->tag->id], 'images' => []]);
        // タグと画像の関連付け
        MemoService::attachTagsAndImages($requestWithNoImages, $memoWithNoImages->id);

        // タグが関連付けられていることを確認
        $this->assertDatabaseHas('memo_tags', ['tag_id' => $this->tag->id, 'memo_id' => $memoWithNoImages->id]);
        // 画像が関連付けられていないことを確認
        $this->assertDatabaseMissing('memo_images', ['memo_id' => $memoWithNoImages->id]);
    }

    /**
     * メモ更新機能のテスト
     * @return void
     */
    public function testUpdateMemo()
    {
        // メモ更新のためのリクエストを作成
        $request = new Request(['memoId' => $this->memo->id, 'title' => '更新されたタイトル', 'content' => '更新された内容']);
        // メモを更新
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
        // share_settings レコードを作成し、必要なすべてのフィールドに値を設定
        $this->memo->shareSettings()->create([
            'sharing_user_id' => $this->otherUser->id,
            'edit_access' => true
        ]);
        // メモの共有状態をチェック
        $checkedMemo = MemoService::checkShared($this->memo);

        // メモのステータスが「共有中」となっていることを確認
        $this->assertEquals('共有中', $checkedMemo->status);
    }
}
