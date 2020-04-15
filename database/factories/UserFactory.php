<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Character;
use App\Inventory;
use App\ItemPrototype;
use App\CharacterClass;
use App\Location;
use App\Modules\Character\Application\Contracts\CharacterRepositoryInterface;
use App\Modules\Equipment\Application\Contracts\InventoryRepositoryInterface;
use App\Modules\Equipment\Application\Contracts\ItemRepositoryInterface;
use App\User;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;
use App\Modules\Character\Domain\HitPoints;
use App\Modules\Character\Infrastructure\Repositories\CharacterClassRepository;
/** @var Factory $factory */

$factory->define(App\User::class, static function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => Str::random(10),
    ];
});


$factory->define(Inventory::class, static function () {

    /** @var InventoryRepositoryInterface $inventoryRepository */
    $inventoryRepository = resolve(InventoryRepositoryInterface::class);
    /** @var \App\Item $item */

    return [
        'id' => $inventoryRepository->nextIdentity()->toString(),
    ];
});

$factory->define(Character::class, static function (Faker\Generator $faker) {

    /** @var CharacterRepositoryInterface $characterRepository */
    $characterRepository = resolve(CharacterRepositoryInterface::class);

    /** @var CharacterClass $characterClassModel */
    $characterClassModel = CharacterClass::query()->inRandomOrder()->first();

    $location = Location::query()->inRandomOrder()->first();

    $characterClass = (new CharacterClassRepository())->getOne($characterClassModel->getId());

    $hitPoints = HitPoints::byCharacterClass($characterClass);

    $genders = ['male', 'female'];

    $characterId = $characterRepository->nextIdentity()->toString();

    return [
        'id' => $characterId,

        'level_id' => 1,

        'location_id' => $location,

        'character_class_id' => $characterClass->getId(),
        'name' => $faker->name,
        'gender' => $genders[array_rand($genders)],

        'xp' => 0,
        'money' => random_int(0, 5000),
        'reputation' => 0,

        // attributes
        'strength' => $characterClass->getStrength(),
        'agility' => $characterClass->getAgility(),
        'stamina' => $characterClass->getStamina(),
        'intelligence' => $characterClass->getIntelligence(),

        'hit_points' => $hitPoints->getCurrentHitPoints(),
        'total_hit_points' => $hitPoints->getMaximumHitPoints(),

        'user_id' => static function () {
            return factory(User::class)->create()->id;
        },
    ];
});

$factory->define(App\Item::class, static function () {

    /** @var ItemRepositoryInterface $itemRepository */
    $itemRepository = resolve(ItemRepositoryInterface::class);

    /** @var ItemPrototype $itemPrototype */
    $itemPrototype = ItemPrototype::query()->inRandomOrder()->first();

    $itemId = $itemRepository->nextIdentity()->toString();

    $itemData = [
        'id' => $itemId,
        'name' => $itemPrototype->getName(),
        'description' => $itemPrototype->getDescription(),
        'effects' => $itemPrototype->getEffects(),
        'price' => $itemPrototype->getPrice(),
        'type' => $itemPrototype->getType(),
        'prototype_id' => $itemPrototype->getId(),
    ];

    return $itemData;
});