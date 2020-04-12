<?php

use App\User;
use Illuminate\Database\Seeder;

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
                "name"          => "Misha Chekin",
                "password"      =>  bcrypt('Catharsis'),
                "email"         => "mchekin@gmail.com"
            ],
        ];

        foreach ($users as $user)
        {
            User::query()->create($user);
        }
    }
}