<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoTagSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('memo_tags')->insert([
            //ユーザー１のダミーデータ
            [
                'memo_id' => '1',
                'tag_id' => '1',
            ],
            [
                'memo_id' => '2',
                'tag_id' => '2',
            ],
            [
                'memo_id' => '3',
                'tag_id' => '3',
            ],
            [
                'memo_id' => '4',
                'tag_id' => '4',
            ],
            [
                'memo_id' => '5',
                'tag_id' => '5',
            ],
            [
                'memo_id' => '6',
                'tag_id' => '6',
            ],
            [
                'memo_id' => '7',
                'tag_id' => '7',
            ],
            [
                'memo_id' => '8',
                'tag_id' => '8',
            ],
            [
                'memo_id' => '9',
                'tag_id' => '9',
            ],
            [
                'memo_id' => '10',
                'tag_id' => '10',
            ],

            //ユーザー２のダミーデータ
            [
                'memo_id' => '11',
                'tag_id' => '11',
            ],
            [
                'memo_id' => '12',
                'tag_id' => '12',
            ],
            [
                'memo_id' => '13',
                'tag_id' => '13',
            ],
            [
                'memo_id' => '14',
                'tag_id' => '14',
            ],
            [
                'memo_id' => '15',
                'tag_id' => '15',
            ],

            //ユーザー３のダミーデータ
            [
                'memo_id' => '16',
                'tag_id' => '16',
            ],
            [
                'memo_id' => '17',
                'tag_id' => '17',
            ],
            [
                'memo_id' => '18',
                'tag_id' => '18',
            ],
            [
                'memo_id' => '19',
                'tag_id' => '19',
            ],
            [
                'memo_id' => '20',
                'tag_id' => '20',
            ],
        ]);
    }
}
