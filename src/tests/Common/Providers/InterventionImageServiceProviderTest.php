<?php

namespace Tests\Common\Providers;

use Illuminate\Support\Facades\App;
use Intervention\Image\ImageManager;
use Tests\Admin\TestCase;

class InterventionImageServiceProviderTest extends TestCase
{
    /**
     * ImageManagerがサービスコンテナに正しくバインドされているかをテスト
     * @return void
     */
    public function testImageManagerIsBoundInTheContainer()
    {
        // サービスコンテナからImageManagerインスタンスを取得
        $imageManager = App::make(ImageManager::class);

        // 取得したインスタンスがImageManagerクラスであることを確認
        $this->assertInstanceOf(ImageManager::class, $imageManager);
    }
}
