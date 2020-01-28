<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CharacterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('character_classes')->insert(
            [
            [
                'name' => 'Воин'
            ],
            [
                'name' => 'Лучник'
            ],
            [
                'name' => 'Целитель'
            ],
            ]
        );
    }
}
