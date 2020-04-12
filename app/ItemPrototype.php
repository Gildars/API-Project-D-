<?php

namespace App;

use App\Traits\UsesStringId;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string id
 * @property string name
 * @property string $description
 * @property string image_file_path
 * @property string type
 * @property array effects
 * @property int price
 */
class ItemPrototype extends Model
{
    use UsesStringId;

    protected $guarded = [];

    protected $casts = [
        'effects' => 'array'
    ];

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getEffects(): array
    {
        return $this->effects;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
