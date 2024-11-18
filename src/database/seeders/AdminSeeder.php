<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'name' => '管理者',
                'email' => 'test@test',
                'password' => Hash::make('laravel321'),
                'created_at' => '2023/010/01/ 11:11:11'
            ]
        ]);
    }
}
