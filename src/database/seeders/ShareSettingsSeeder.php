<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShareSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('share_settings')->insert([
            //ユーザー１のダミーデータ
            [
                'sharing_user_id' => '2',
                'memo_id' => '1',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '2',
                'memo_id' => '2',
                'edit_access' => '0',
            ],
            [
                'sharing_user_id' => '3',
                'memo_id' => '3',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '3',
                'memo_id' => '4',
                'edit_access' => '0',
            ],
            [
                'sharing_user_id' => '2',
                'memo_id' => '5',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '3',
                'memo_id' => '5',
                'edit_access' => '1',
            ],

            //ユーザー２のダミーデータ
            [
                'sharing_user_id' => '1',
                'memo_id' => '11',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '1',
                'memo_id' => '12',
                'edit_access' => '0',
            ],
            [
                'sharing_user_id' => '3',
                'memo_id' => '13',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '3',
                'memo_id' => '14',
                'edit_access' => '0',
            ],
            [
                'sharing_user_id' => '1',
                'memo_id' => '14',
                'edit_access' => '1',
            ],

            //ユーザー３のダミーデータ
            [
                'sharing_user_id' => '1',
                'memo_id' => '16',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '1',
                'memo_id' => '17',
                'edit_access' => '0',
            ],
            [
                'sharing_user_id' => '2',
                'memo_id' => '18',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '2',
                'memo_id' => '19',
                'edit_access' => '0',
            ],
            [
                'sharing_user_id' => '1',
                'memo_id' => '19',
                'edit_access' => '0',
            ],
            //ユーザー４のダミーデータ
            [
                'sharing_user_id' => '1',
                'memo_id' => '21',
                'edit_access' => '1',
            ],
            [
                'sharing_user_id' => '2',
                'memo_id' => '21',
                'edit_access' => '1',
            ],
            //ユーザー５のダミーデータ
            [
                'sharing_user_id' => '4',
                'memo_id' => '26',
                'edit_access' => '1',
            ],
        ]);
    }
}
