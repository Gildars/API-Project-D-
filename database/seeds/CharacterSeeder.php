<?php

use App\Character;
use App\Inventory;
use App\Item;
use App\ItemPrototype;
use App\Location;
use App\Modules\Character\Application\Contracts\CharacterRepositoryInterface;
use App\Modules\Equipment\Application\Contracts\InventoryRepositoryInterface;
use App\Modules\Equipment\Application\Contracts\ItemRepositoryInterface;
use App\Modules\Equipment\Domain\ItemStatus;
use App\User;
use Illuminate\Database\Seeder;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('characters')->delete();

        /** @var CharacterRepositoryInterface $characterRepository */
        $characterRepository = resolve(CharacterRepositoryInterface::class);

        /** @var InventoryRepositoryInterface $inventoryRepository */
        $inventoryRepository = resolve(InventoryRepositoryInterface::class);

        /** @var ItemRepositoryInterface $itemRepository */
        $itemRepository = resolve(ItemRepositoryInterface::class);

        $totalHitPoints = 100;

        /** @var User $user */
        $user = User::query()->first();

        /** @var Location $location */
        $location = Location::query()->firstOrFail();

        /** @var Character $someone */
        $someone = Character::query()->create([
            'id' => $characterRepository->nextIdentity()->toString(),
            'name' => 'Someone',
            'gender' => 'male',

            'xp' => 0,
            'reputation' => 0,
            'hit_points' => $totalHitPoints,
            'total_hit_points' => $totalHitPoints,
            'money' => 100,

            'strength' => 5,
            'agility' => 5,
            'constitution' => 5,
            'intelligence' => 5,
            'charisma' => 1,

            'level_id' => 1,
            'user_id' => $user->getId(),
            'location_id' => $location->getId(),
            //'race_id' => 1,
        ]);

        /** @var Inventory $inventory */
        $inventory = Inventory::query()->create([
            'id' => $inventoryRepository->nextIdentity()->toString(),
            'character_id' => $someone->getId(),
        ]);

        ItemPrototype::query()->get()
            ->each(static function (ItemPrototype $weaponPrototype, int $slot) use ($someone, $inventory, $itemRepository) {

                /** @var Item $item */
                $item = Item::query()->create([
                    'id' => $itemRepository->nextIdentity()->toString(),
                    'name' => $weaponPrototype->getName(),
                    'description' => $weaponPrototype->getDescription(),
                    'effects' => $weaponPrototype->getEffects(),
                    'price' => $weaponPrototype->getPrice(),
                    'type' => $weaponPrototype->getType(),
                    'prototype_id' => $weaponPrototype->getId(),
                    'creator_character_id' => $someone->getId(),
                ]);

                $inventory->items()->attach($item->getId(), [
                    'inventory_slot_number' => $slot,
                    'status' => $slot ? ItemStatus::IN_BACKPACK : ItemStatus::EQUIPPED,
                ]);
            });

        factory(Character::class, 50)->create();
        factory(Item::class, 50)->create();
    }
}
