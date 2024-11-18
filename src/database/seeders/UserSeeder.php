<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'ユーザー１',
                'email' => 'test@test1',
                'password' => Hash::make('laravel321'),
                'created_at' => '2023/010/01/ 11:11:11'
            ],
            [
                'name' => 'ユーザー２',
                'email' => 'test@test2',
                'password' => Hash::make('laravel321'),
                'created_at' => '2023/010/02/ 11:11:11'
            ],
            [
                'name' => 'ユーザー３',
                'email' => 'test@test3',
                'password' => Hash::make('laravel321'),
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'name' => 'ユーザー４',
                'email' => 'test@test4',
                'password' => Hash::make('laravel321'),
                'created_at' => '2023/010/03/ 11:11:11'
            ],
            [
                'name' => 'ユーザー５',
                'email' => 'test@test5',
                'password' => Hash::make('laravel321'),
                'created_at' => '2023/010/03/ 11:11:11'
            ],
        ]);
    }
}
