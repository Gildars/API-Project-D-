<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                [
                    'name' => 'TestOne',
                    'email' => 'test1@gmail.com',
                    'password' => bcrypt('Catharsis'),
                    'gender' => 'male',
                    'classId' => 1
                ],
                [
                    'name' => 'TestTwo',
                    'email' => 'test2@gmail.com',
                    'password' => bcrypt('Catharsis'),
                    'gender' => 'female',
                    'classId' => 0
                ]
            ]
        );
    }
}
