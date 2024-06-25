<?php

namespace Tests\User\Unit\Models;

use App\Models\Image;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\User\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Memo $memo;
    private Image $image;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー、メモ、画像を作成
        $this->user = User::factory()->create();
        $this->memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $this->image = Image::factory()->create(['user_id' => $this->user->id]);

        // 認証ユーザーとして設定
        Auth::shouldReceive('id')->andReturn($this->user->id);

        // リレーションを設定
        $this->image->memos()->attach($this->memo);
    }

    /**
     * Imageモデルの基本的なリレーションが正しく機能しているかのテスト
     * @return void
     */
    public function testImageAttributesAndRelations()
    {
        // 画像に関連付けられたメモが、正しいかを確認
        $this->assertTrue($this->image->memos->contains($this->memo));
        // 画像に関連付けられたメモのIDが、正しいかを確認
        $this->assertEquals($this->memo->id, $this->image->memos->first()->id);

        // 画像に関連付けられたユーザーが、正しいかを確認
        $this->assertInstanceOf(User::class, $this->image->user);
        // 画像に関連付けられたユーザーのIDが、正しいかを確認
        $this->assertEquals($this->user->id, $this->image->user->id);
    }

    /**
     * 自分自身の全ての画像のデータを取得するスコープのテスト
     * @return void
     */
    public function testAvailableAllImagesScope()
    {
        $images = Image::availableAllImages()->get();

        // 取得した画像の中に、テスト用画像が含まれているかを確認
        $this->assertTrue($images->contains($this->image));
    }

    /**
     * 自分自身の選択した画像のデータを取得するスコープのテスト
     * @return void
     */
    public function testAvailableSelectImageScope()
    {
        $selectedImage = Image::availableSelectImage($this->image->id)->first();

        // 取得した画像のIDが、テスト用の画像IDと一致するか確認
        $this->assertEquals($this->image->id, $selectedImage->id);
    }

    /**
     * 画像をDBに保存するスコープのテスト
     * @return void
     */
    public function testAvailableCreateImageScope()
    {
        // 新しい画像の名前を設定
        $fileName = 'new_image.jpg';
        // 新しい画像を作成
        Image::availableCreateImage($fileName);

        // 作成された画像がDBに存在するかを確認
        $this->assertDatabaseHas('images', ['filename' => $fileName, 'user_id' => $this->user->id,]);
    }
}
