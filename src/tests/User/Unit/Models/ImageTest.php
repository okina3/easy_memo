<?php

namespace Tests\User\Unit\Models;

use App\Models\Image;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class ImageTest extends TestCase
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
     * 画像を作成するヘルパーメソッド
     * @param int $count 画像の作成数
     * @return Collection 作成された画像のコレクション
     */
    private function createImages(int $count): Collection
    {
        // 指定された数の画像を、現在のユーザーに関連付けて作成する
        return Image::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    /**
     * 画像にメモを関連付けるヘルパーメソッド
     * @param Image $image 関連付ける画像
     * @param int $memoCount 作成するメモの数
     * @return Collection 作成されたメモのコレクション
     */
    private function attachMemos(Image $image, int $memoCount): Collection
    {
        // メモを作成し、画像に関連付け
        $memos = Memo::factory()->count($memoCount)->create();
        $image->memos()->attach($memos);

        // 作成されたメモのコレクションを返す
        return $memos;
    }

    /**
     * 基本的なリレーションが、正しく機能しているかのテスト
     * @return void
     */
    public function testImageAttributesAndRelations()
    {
        // 1件の画像を作成
        $image = $this->createImages(1)->first();
        // 画像に2件のメモを関連付け
        $attachedMemos = $this->attachMemos($image, 2);

        // 画像に関連付けられたメモのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $image->memos());
        // 作成した全てのメモのIDが、画像に関連付けられたメモのIDと一致しているかを確認
        $this->assertEquals($attachedMemos->pluck('id')->toArray(), $image->memos->pluck('id')->toArray());

        // 画像に関連付けられたユーザーのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $image->user());
        // 画像に関連付けられたユーザーのIDが、作成したユーザーのIDと一致しているかを確認
        $this->assertEquals($this->user->id, $image->user->id);
    }

    /**
     * 自分自身の全ての画像のデータを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllImagesScope()
    {
        // 3件の画像を作成
        $images = $this->createImages(3);
        // 全ての画像を取得
        $allImages = Image::availableAllImages()->get();

        // 作成した画像のIDの配列が、取得した画像のIDの配列と、一致するか確認
        $this->assertEquals($images->pluck('id')->toArray(), $allImages->pluck('id')->toArray());
    }

    /**
     * 自分自身の選択した画像のデータを、取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectImageScope()
    {
        // 1件の画像を作成
        $image = $this->createImages(1)->first();
        // 選択した画像を取得
        $selectedImage = Image::availableSelectImage($image->id)->first();

        // 作成した画像のIDが、取得した画像のIDと、一致するか確認
        $this->assertEquals($image->id, $selectedImage->id);
    }

    /**
     * 画像をDBに、保存するスコープのテスト
     * @return void
     */
    public function testAvailableCreateImageScope()
    {
        // 新しい画像の名前を作成
        $fileName = 'new_image.jpg';
        // 新しい画像を保存
        Image::availableCreateImage($fileName);

        // 作成された画像がDBに存在するかを確認
        $this->assertDatabaseHas('images', ['filename' => $fileName, 'user_id' => $this->user->id,]);
    }
}
