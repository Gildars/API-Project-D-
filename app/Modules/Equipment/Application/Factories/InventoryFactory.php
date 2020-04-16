<?php

namespace App\Modules\Equipment\Application\Factories;

use App\Modules\Character\Domain\CharacterId;
use App\Modules\Equipment\Domain\Item;
use App\Modules\Equipment\Domain\ItemId;
use App\Modules\Equipment\Domain\ItemPrototype;


class InventoryFactory
{
    public function create(ItemId $itemId, ItemPrototype $itemPrototype, CharacterId $creatorCharacterId): Item
    {
        return new Item(
            $itemId,
            $itemPrototype->getName(),
            $itemPrototype->getDescription(),
            $itemPrototype->getType(),
            $itemPrototype->getEffects(),
            $itemPrototype->getPrice(),
            $itemPrototype->getId(),
            $creatorCharacterId
        );
    }
}
