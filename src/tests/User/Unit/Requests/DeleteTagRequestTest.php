<?php

namespace Tests\User\Unit\Requests;

use App\Http\Requests\DeleteTagRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\User\TestCase;

class DeleteTagRequestTest extends TestCase
{
    use RefreshDatabase;

    private DeleteTagRequest $request;

    /**
     * テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // DeleteTagRequestのインスタンスを作成、テスト用のリクエストオブジェクトを初期化
        $this->request = new DeleteTagRequest();
    }

    /**
     * 指定されたデータを用いてDeleteTagRequestのインスタンスを作成。
     * テスト用に、リクエストにデータをマージするメソッド
     *
     * @param array $data
     * @return DeleteTagRequest
     */
    private function createDeleteTagRequest(array $data): DeleteTagRequest
    {
        // 受け取ったデータをリクエストオブジェクトに統合
        $this->request->merge($data);
        return $this->request;
    }

    /**
     * リクエストが承認されるかどうかを確認するテスト
     *
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        // リクエストが常に承認されることを検証、trueを返すことを確認
        $this->assertTrue($this->request->authorize());
    }

    /**
     * リクエストデータが正しくバリデートされるかを確認するテスト
     *
     * @return void
     */
    public function testRulesValidation()
    {
        // テスト用データ
        $data = ['tags' => [1, 2, 3]];

        // リクエストを作成
        $request = $this->createDeleteTagRequest($data);
        // バリデータを作成
        $validator = Validator::make($request->all(), $request->rules());

        // バリデーションが成功することを確認
        $this->assertTrue($validator->passes());
    }

    /**
     * バリデーションエラーメッセージが正しく設定されているかを確認するテスト
     *
     * @return void
     */
    public function testMessagesMethod()
    {
        // リクエストからバリデーションメッセージを取得
        $messages = $this->request->messages();

        // 期待されるバリデーションメッセージを定義
        $expectedMessages = [
            'tags.required' => '削除したいタグに、チェックを入れてください。',
        ];

        // 取得したメッセージが期待されるものと一致することを確認
        $this->assertEquals($expectedMessages, $messages);
    }
}