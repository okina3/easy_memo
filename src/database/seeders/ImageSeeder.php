<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('images')->insert([
            [
                //ユーザー１のダミーデータ
                'user_id' => '1',
                'filename' => 'sample_1.jpg',
            ],
            [
                'user_id' => '1',
                'filename' => 'sample_2.jpg',
            ],
            [
                'user_id' => '1',
                'filename' => 'sample_3.jpg',
            ],
            [
                'user_id' => '1',
                'filename' => 'sample_4.jpg',
            ],
            [
                'user_id' => '1',
                'filename' => 'sample_5.jpg',
            ],

            //ユーザー２のダミーデータ
            [
                'user_id' => '2',
                'filename' => 'sample_6.jpg',
            ],
            [
                'user_id' => '2',
                'filename' => 'sample_7.jpg',
            ],
            [
                'user_id' => '2',
                'filename' => 'sample_8.jpg',
            ],

            //ユーザー３のダミーデータ
            [
                'user_id' => '3',
                'filename' => 'sample_9.jpg',
            ],
            [
                'user_id' => '3',
                'filename' => 'sample_10.jpg',
            ],
            [
                'user_id' => '3',
                'filename' => 'sample_11.jpg',
            ],
        ]);
    }
}
