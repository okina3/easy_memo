<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('memos')->insert([
            //ユーザー１のダミーデータ
            [
                'title' => 'タイトル１',
                'content' => '共有なし。ユーザー１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１',
                'user_id' => '1',
                'deleted_at' => null,
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル２',
                'content' => 'ユーザー２に共有。ユーザー１、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２',
                'user_id' => '1',
                'deleted_at' => null,
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル３',
                'content' => 'ユーザー２とユーザー３に共有。ユーザー１、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、、テスト３、、テスト３、、テスト３、、テスト３、、テスト３、、テスト３、、テスト３、、テスト３、',
                'user_id' => '1',
                'deleted_at' => null,
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル４',
                'content' => 'ユーザー１、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４、テスト４',
                'user_id' => '1',
                'deleted_at' => null,
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル５',
                'content' => 'ユーザー１、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５、テスト５',
                'user_id' => '1',
                'deleted_at' => null,
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル６',
                'content' => 'ユーザー１、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６、テスト６',
                'user_id' => '1',
                'deleted_at' => null,
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル７',
                'content' => 'ユーザー１、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７、テスト７',
                'user_id' => '1',
                'deleted_at' => null,
                'created_at' => '2023/010/01/ 11:11:11'
            ],

            [
                'title' => 'タイトル８',
                'content' => 'ユーザー１、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用',
                'user_id' => '1',
                'deleted_at' => '2023/010/02/ 22:22:22',
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル９',
                'content' => 'ユーザー１、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用',
                'user_id' => '1',
                'deleted_at' => '2023/010/02/ 22:22:22',
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'title' => 'タイトル１０',
                'content' => 'ユーザー１、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用、ソフトデリート用',
                'user_id' => '1',
                'deleted_at' => '2023/010/02/ 22:22:22',
                'created_at' => '2023/010/01/ 11:11:11'
            ],

            //ユーザー２のダミーデータ
            [
                'title' => 'タイトル１１',
                'content' => '共有なし。ユーザー２、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１',
                'user_id' => '2',
                'deleted_at' => null,
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'title' => 'タイトル１２',
                'content' => 'ユーザー１に共有。ユーザー２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２',
                'user_id' => '2',
                'deleted_at' => null,
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'title' => 'タイトル１３',
                'content' => 'ユーザー１とユーザー３に共有。ユーザー２、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３',
                'user_id' => '2',
                'deleted_at' => null,
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'title' => 'タイトル１４',
                'content' => 'ユーザー１に共有。ユーザー２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２',
                'user_id' => '2',
                'deleted_at' => null,
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'title' => 'タイトル１５',
                'content' => 'ユーザー１とユーザー３に共有。ユーザー２、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３',
                'user_id' => '2',
                'deleted_at' => null,
                'created_at' => '2023/010/02/ 11:11:11'
            ],

            //ユーザー３のダミーデータ
            [
                'title' => 'タイトル１６',
                'content' => '共有なし。ユーザー３、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１、テスト１',
                'user_id' => '3',
                'deleted_at' => null,
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'title' => 'タイトル１７',
                'content' => 'ユーザー１とユーザー２に共有。ユーザー３、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２',
                'user_id' => '3',
                'deleted_at' => null,
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'title' => 'タイトル１８',
                'content' => 'ユーザー１とユーザー２に共有。ユーザー３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３',
                'user_id' => '3',
                'deleted_at' => null,
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'title' => 'タイトル１９',
                'content' => 'ユーザー１とユーザー２に共有。ユーザー３、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２、テスト２',
                'user_id' => '3',
                'deleted_at' => null,
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'title' => 'タイトル２０',
                'content' => 'ユーザー１とユーザー２に共有。ユーザー３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３、テスト３',
                'user_id' => '3',
                'deleted_at' => null,
                'created_at' => '2023/010/03/ 11:11:11'
            ],
        ]);
    }
}