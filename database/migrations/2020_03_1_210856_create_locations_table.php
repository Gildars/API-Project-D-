<?php

use App\Location;
use App\Modules\Character\Application\Contracts\LocationRepositoryInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', static function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name')->unique();
            $table->string('description');

            $table->timestamps();
        });

        /** @var LocationRepositoryInterface $locationRepository */
        $locationRepository = resolve(LocationRepositoryInterface::class);

        $locations = [
            [
                'id' => $locationRepository->nextIdentity()->toString(),
                'name' => 'Inn',
                'description' => 'An establishment or building providing lodging and, usually, food and drink for travelers (Starting location)'
            ],
            array(
                'id' => $locationRepository->nextIdentity()->toString(),
                'name' => 'Town Hall',
                'description' => 'Public forum or meeting in which those attending gather to discuss civic or political issues, hear and ask questions about the ideas of a candidate for public office'
            ),
            [
                'id' => $locationRepository->nextIdentity()->toString(),
                'name' => 'Smithy',
                'description' => "A blacksmith's shop. A place to purchase weaponry and armor or train one's skill as a blacksmith"
            ],
            [
                'id' => $locationRepository->nextIdentity()->toString(),
                'name' => 'Military academy fortress',
                'description' => 'An institute where soldiers and mercenaries train they martial skills'
            ],
        ];

        foreach ($locations as $location) {
            Location::query()->forceCreate($location);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}