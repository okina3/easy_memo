<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\User\TestCase;

class ShareSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $secondaryUser;
    private User $tertiaryUser;
    private Memo $secondaryUserMemo;
    private Memo $tertiaryUserMemo;

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
        // 3人目のユーザーを作成
        $this->tertiaryUser = User::factory()->create();

        // 認証済みのユーザーを返す
        $this->actingAs($this->user);

        // ユーザーのメモ作成
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 2人目の別のユーザーのメモ作成
        $this->secondaryUserMemo = Memo::factory()->create(['user_id' => $this->secondaryUser->id]);
        // 3人目のユーザーのメモ作成
        $this->tertiaryUserMemo = Memo::factory()->create(['user_id' => $this->tertiaryUser->id]);
    }

    /**
     * 指定されたメモを自分に共有させる設定を作成するヘルパーメソッド
     * @param Memo $memo
     * @return ShareSetting|Collection|Model
     */
    private function createSharedMemo(Memo $memo): Model|Collection|ShareSetting
    {
        return ShareSetting::factory()->create([
            // 自分に共有
            'sharing_user_id' => $this->user->id,
            // 別のユーザーのメモ
            'memo_id' => $memo->id,
            // 編集も可能
            'edit_access' => true,
        ]);
    }

    /**
     * 他人のメモにタグと画像を関連付けるヘルパーメソッド
     * @param ShareSetting $shareSetting 関連付けるシェア設定
     * @param int $tagCount 作成するタグの数
     * @param int $imageCount 作成する画像の数
     * @return array タグと画像の配列
     */
    private function attachTagsAndImages(ShareSetting $shareSetting, int $tagCount = 2, int $imageCount = 2): array
    {
        // シェア設定からメモを取得
        $memo = $shareSetting->memo;

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
     * 自分に共有されたメモの一覧表示が、正しく動作することをテスト
     * @return void
     */
    public function testIndexShareSettingController()
    {
        // 2件の他人のメモを、自分に共有する設定を作成
        $shareSettings = collect([
            $this->createSharedMemo($this->secondaryUserMemo),
            $this->createSharedMemo($this->tertiaryUserMemo)
        ]);

        // 自分に共有された他人のメモの一覧を表示する為に、リクエストを送信
        $response = $this->get(route('user.share-setting.index'));

        // レスポンスが 'user.shareSettings.index' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.shareSettings.index');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('shared_memos', function ($viewSharedMemo) use ($shareSettings) {
            // ビューから取得した共有されたメモをコレクションに変換
            $viewSharedMemo = collect($viewSharedMemo);
            // ビューに渡される共有メモが、2件、かつ、共有メモのID配列と、作成した共有設定のmemo_id配列と一致することを確認
            return $viewSharedMemo->count() === 2 &&
                $viewSharedMemo->pluck('id')->toArray() === $shareSettings->pluck('memo_id')->toArray();
        });
        $response->assertViewHas('shared_users', function ($viewUsers) use ($shareSettings) {
            // ビューから取得したユーザーをコレクションに変換
            $viewUsers = collect($viewUsers);
            // メモに関連するユーザーを一意に取得
            $sharedUsers = $shareSettings->pluck('memo.user')->unique();
            // ビューに渡されるユーザーが、2件、かつ、ユーザーID配列と、作成したメモに関連するユーザーID配列と一致することを確認
            return $viewUsers->count() === 2 &&
                $viewUsers->pluck('id')->toArray() === $sharedUsers->pluck('id')->toArray();
        });
    }

    /**
     * 自分のメモの共有設定が、正しく保存されることをテスト
     * @return void
     */
    public function testStoreShareSettingController()
    {
        // 1件の共有設定のデータを作成（自分のメモを、2人目の別のユーザーに共有）
        $requestData = [
            'share_user_start' => $this->secondaryUser->email,
            'memoId' => $this->memo->id,
            'edit_access' => true,
        ];

        // メモを共有する為に、リクエストを送信
        $response = $this->post(route('user.share-setting.store'), $requestData);

        // 共有設定が保存されているか確認
        $this->assertDatabaseHas('share_settings', [
            'sharing_user_id' => $this->secondaryUser->id,
            'memo_id' => $this->memo->id,
            'edit_access' => true,
        ]);

        // レスポンスが 'index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを共有しました。', 'status' => 'info']);
    }
    // --------------------------------------------------------------------------------------------------------
    // /**
    //  * 自分のメモの共有設定が、正しく保存される時のエラーハンドリングをテスト
    //  * @return void
    //  */
    // public function testErrorStoreShareSettingController()
    // {
    //     // 1件の共有設定のデータを作成（自分のメモを、2人目の別のユーザーに共有）
    //     $requestData = [
    //         'share_user_start' => $this->secondaryUser->email,
    //         'memoId' => $this->memo->id,
    //         'edit_access' => true,
    //     ];

    //     // DB::transactionメソッドが呼び出されると、一度だけ例外をスローするように設定
    //     DB::shouldReceive('transaction')->once()->andThrow(new Exception('DBエラー'));

    //     // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
    //     Log::shouldReceive('error')->once()->with(Mockery::type(Exception::class));

    //     // 例外がスローされることを期待し、そのメッセージが"DBエラー"であることを確認
    //     $this->expectException(Exception::class);
    //     $this->expectExceptionMessage('DBエラー');

    //     // メモを共有する為に、リクエストを送信
    //     $this->post(route('user.share-setting.store'), $requestData);
    // }
    // --------------------------------------------------------------------------------------------------------
    /**
     * 自分に共有されたメモの詳細表示が、正しく動作することをテスト
     * @return void
     */
    public function testShowShareSettingController()
    {
        // 1件の他人のメモを、自分に共有する設定を作成
        $shareSetting = $this->createSharedMemo($this->secondaryUserMemo);
        // 自分に共有された他人のメモに、2件のタグと画像を関連付け
        [$attachedTags, $attachedImages] = $this->attachTagsAndImages($shareSetting);

        // 共有メモ詳細画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.share-setting.show', ['share' => $shareSetting->memo_id]));

        // レスポンスが 'user.shareSettings.show' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.shareSettings.show');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_memo', function ($viewSharedMemo) use ($shareSetting) {
            // ビューに渡される共有メモのIDが、作成した共有メモのIDと一致することを確認
            return $viewSharedMemo->id === $shareSetting->memo_id;
        });
        $response->assertViewHas('get_memo_tags_name', function ($viewSharedTags) use ($attachedTags) {
            // ビューから取得したタグをコレクションに変換
            $viewSharedTags = collect($viewSharedTags);
            // ビューに渡されるタグが、2件であり、かつ、タグのNameの配列も、一致することを確認
            return $viewSharedTags->count() === 2 &&
                $viewSharedTags->toArray() === $attachedTags->pluck('name')->toArray();
        });
        $response->assertViewHas('get_memo_images', function ($viewSharedImages) use ($attachedImages) {
            // ビューから取得したタグをコレクションに変換
            $viewSharedImages = collect($viewSharedImages);
            // ビューに渡される画像が、2件であり、かつ、画像のIDの配列も、一致することを確認
            return $viewSharedImages->count() === 2 &&
                $viewSharedImages->pluck('id')->toArray() === $attachedImages->pluck('id')->toArray();
        });
        $response->assertViewHas('select_user', function ($viewSharedUser) use ($shareSetting) {
            // ビューに渡される共有メモのユーザーIDが、作成した共有メモのユーザーIDと一致することを確認
            return $viewSharedUser->id === $shareSetting->memo->user_id;
        });
    }

    /**
     * 自分に共有されたメモの編集表示が、正しく動作することをテスト
     * @return void
     */
    public function testEditShareSettingController()
    {
        // 1件の他人のメモを、自分に共有する設定を作成
        $shareSetting = $this->createSharedMemo($this->secondaryUserMemo);
        // 自分に共有された他人のメモに2件のタグと画像を関連付け
        [$attachedTags, $attachedImages] = $this->attachTagsAndImages($shareSetting);

        // 共有メモ編集画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.share-setting.edit', ['share' => $shareSetting->memo_id]));

        // レスポンスが 'user.shareSettings.edit' ビューを返すことを確認
        $response->assertStatus(200);
        $response->assertViewIs('user.shareSettings.edit');

        // ビューに渡されるデータが正しいか確認
        $response->assertViewHas('select_memo', function ($viewSharedMemo) use ($shareSetting) {
            // ビューに渡される共有メモのIDが、作成した共有メモのIDと一致することを確認
            return $viewSharedMemo->id === $shareSetting->memo_id;
        });
        $response->assertViewHas('get_memo_tags_name', function ($viewSharedTags) use ($attachedTags) {
            // ビューから取得したタグをコレクションに変換
            $viewSharedTags = collect($viewSharedTags);
            // ビューに渡されるタグが、2件であり、かつ、タグのNameの配列も、一致することを確認
            return $viewSharedTags->count() === 2 &&
                $viewSharedTags->toArray() === $attachedTags->pluck('name')->toArray();
        });
        $response->assertViewHas('get_memo_images', function ($viewSharedImages) use ($attachedImages) {
            // ビューから取得したタグをコレクションに変換
            $viewSharedImages = collect($viewSharedImages);
            // ビューに渡される画像が、2件であり、かつ、画像のIDの配列も、一致することを確認
            return $viewSharedImages->count() === 2 &&
                $viewSharedImages->pluck('id')->toArray() === $attachedImages->pluck('id')->toArray();
        });
        $response->assertViewHas('select_user', function ($viewSharedUser) use ($shareSetting) {
            // ビューに渡される共有メモのユーザーIDが、作成した共有メモのユーザーIDと一致することを確認
            return $viewSharedUser->id === $shareSetting->memo->user_id;
        });
    }

    /**
     * 自分に共有されたメモが、正しく更新されることをテスト
     * @return void
     */
    public function testUpdateShareSettingController()
    {
        // 2人目の別のユーザーのメモを更新するデータを作成
        $requestData = ['memoId' => $this->secondaryUserMemo->id, 'content' => '他人のメモの更新テスト',];

        // 共有メモ更新する為に、リクエストを送信
        $response = $this->patch(route('user.share-setting.update'), $requestData);

        // 2人目の別のユーザーのメモが更新されたことを確認
        $this->assertDatabaseHas('memos', [
            'id' => $this->secondaryUserMemo->id,
            'content' => '他人のメモの更新テスト',
            'user_id' => $this->secondaryUserMemo->user_id,
        ]);

        // レスポンスが 'share-setting.index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.share-setting.index'));
        $response->assertSessionHas(['message' => '共有されたメモを更新しました。', 'status' => 'info']);
    }

    /**
     * 自分の共有メモが、正しく削除されることをテスト
     * @return void
     */
    public function testDestroyShareSettingController()
    {
        // 1件の共有設定のデータを作成（自分のメモを、2人目の別のユーザーに共有）
        $requestData = [
            'share_user_end' => $this->secondaryUser->email,
            'memoId' => $this->memo->id,
        ];

        // 共有したメモを解除する為に、リクエストを送信
        $response = $this->delete(route('user.share-setting.destroy'), $requestData);

        // 共有設定が削除されているか確認
        $this->assertDatabaseMissing('share_settings', [
            'sharing_user_id' => $this->secondaryUser->id,
            'memo_id' => $this->memo->id,
        ]);

        // レスポンスが 'index' リダイレクト先を指していることを確認
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => '共有を解除しました。', 'status' => 'alert']);
    }
}
