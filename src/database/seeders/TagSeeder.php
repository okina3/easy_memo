<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('tags')->insert([
            //ユーザー１のダミーデータ
            [
                'name' => 'タグ１(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'name' => 'タグ２(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'name' => 'タグ３(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ４(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ５(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ６(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ７(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ８(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ９(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ１０(ユ1)',
                'user_id' => '1',
                'created_at' => '2023/010/02/ 11:11:11'
            ],

            //ユーザー２のダミーデータ
            [
                'name' => 'タグ１(ユ2)',
                'user_id' => '2',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ２(ユ2)',
                'user_id' => '2',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ３(ユ2)',
                'user_id' => '2',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ４(ユ2)',
                'user_id' => '2',
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'タグ５(ユ2)',
                'user_id' => '2',
                'created_at' => '2023/010/02/ 11:11:11'
            ],

            //ユーザー３のダミーデータ
            [
                'name' => 'タグ１(ユ3)',
                'user_id' => '3',
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'name' => 'タグ２(ユ3)',
                'user_id' => '3',
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'name' => 'タグ３(ユ3)',
                'user_id' => '3',
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'name' => 'タグ４(ユ3)',
                'user_id' => '3',
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'name' => 'タグ５(ユ3)',
                'user_id' => '3',
                'created_at' => '2023/010/03/ 11:11:11'
            ],
        ]);
    }
}
