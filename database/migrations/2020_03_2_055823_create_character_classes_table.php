<?php

use App\Location;
use App\CharacterClass;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCharacterClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_classes', function (Blueprint $table) {
            $table->integer('id')->unsigned()->primary();

            $table->string("name");

            // attributes
            $table->integer('strength');
            $table->integer('agility');
            $table->integer('stamina');
            $table->integer('intelligence');

            // locations
            $table->uuid('starting_location_id');
            $table->foreign('starting_location_id')
                ->references('id')
                ->on('locations')
                ->onDelete('restrict');

            $table->timestamps();
        });

        /** @var Location $location */
        $location = Location::query()->firstOrFail();

        $characterClasses = [
            [
                "id" => 1,
                "name" => "Воин",
                "strength" => 15,
                "agility" => 5,
                "stamina" => 5,
                "intelligence" => 5,

                "starting_location_id" => $location->getId(),
            ],
            [
                "id" => 2,
                "name" => "Целитель",
                "strength" => 5,
                "agility" => 9,
                "stamina" => 1,
                "intelligence" => 5,

                "starting_location_id" => $location->getId(),
            ],
            [
                "id" => 3,
                "name" => "Волшебник",
                "strength" => 5,
                "agility" => 1,
                "stamina" => 9,
                "intelligence" => 5,

                "starting_location_id" => $location->getId(),
            ]
        ];

        foreach ($characterClasses as $class)
        {
            CharacterClass::query()->forceCreate($class);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('character_classes');
    }
}
