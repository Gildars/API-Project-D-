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


        factory(Character::class, 10)->create()->each(function ($character) {
            $item = factory(Item::class)->create(['creator_character_id' => $character->id]);
            $inventory = factory(Inventory::class)->create([
                'character_id' => $character->id
            ]);
            $inventory->items()->attach($item->id, [
                'inventory_slot_number' => 0,
                'status' => ItemStatus::EQUIPPED,
            ]);
        });
    }
}
