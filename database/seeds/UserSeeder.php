<?php

use App\User;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $users = [
            [
                "name"          => "Gildarts",
                "password"      => bcrypt("Catharsiscur19"),
                "email"         => "thegilldars@gmail.com",
            ],
        ];

        foreach ($users as $user)
        {
           User::query()->create($user);
        }
    }
}
