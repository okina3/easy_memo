<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoImageSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('memo_images')->insert([
            //ユーザー１のダミーデータ
            [
                'memo_id' => '1',
                'image_id' => '1',
            ],
            [
                'memo_id' => '1',
                'image_id' => '2',
            ],
            [
                'memo_id' => '2',
                'image_id' => '3',
            ],
            [
                'memo_id' => '2',
                'image_id' => '4',
            ],
            [
                'memo_id' => '3',
                'image_id' => '5',
            ],
            [
                'memo_id' => '3',
                'image_id' => '1',
            ],
            [
                'memo_id' => '4',
                'image_id' => '2',
            ],
            [
                'memo_id' => '4',
                'image_id' => '4',
            ],
            [
                'memo_id' => '5',
                'image_id' => '3',
            ],
            [
                'memo_id' => '5',
                'image_id' => '1',
            ],
            [
                'memo_id' => '6',
                'image_id' => '2',
            ],
            [
                'memo_id' => '7',
                'image_id' => '4',
            ],
            [
                'memo_id' => '8',
                'image_id' => '5',
            ],

            //ユーザー２のダミーデータ
            [
                'memo_id' => '11',
                'image_id' => '6',
            ],
            [
                'memo_id' => '11',
                'image_id' => '7',
            ],
            [
                'memo_id' => '12',
                'image_id' => '8',
            ],
            [
                'memo_id' => '12',
                'image_id' => '6',
            ],
            [
                'memo_id' => '13',
                'image_id' => '7',
            ],
            [
                'memo_id' => '13',
                'image_id' => '8',
            ],
            [
                'memo_id' => '14',
                'image_id' => '7',
            ],
            [
                'memo_id' => '15',
                'image_id' => '8',
            ],

            //ユーザー３のダミーデータ
            [
                'memo_id' => '16',
                'image_id' => '9',
            ],
            [
                'memo_id' => '16',
                'image_id' => '10',
            ],
            [
                'memo_id' => '17',
                'image_id' => '11',
            ],
            [
                'memo_id' => '17',
                'image_id' => '9',
            ],
            [
                'memo_id' => '18',
                'image_id' => '10',
            ],
            [
                'memo_id' => '18',
                'image_id' => '11',
            ],
            [
                'memo_id' => '19',
                'image_id' => '10',
            ],
            [
                'memo_id' => '20',
                'image_id' => '9',
            ],
        ]);
    }
}
