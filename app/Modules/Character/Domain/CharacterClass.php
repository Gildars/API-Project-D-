<?php


namespace App\Modules\Character\Domain;

class CharacterClass
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $startingLocationId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var Attributes
     */
    private $attributes;

    public function __construct(
        int $id,
        string $startingLocationId,
        string $name,
        Attributes $attributes
    ) {
        $this->id = $id;
        $this->startingLocationId = $startingLocationId;
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartingLocationId(): string
    {
        return $this->startingLocationId;
    }

    public function getStrength(): int
    {
        return $this->attributes->getStrength();
    }

    public function getAgility(): int
    {
        return $this->attributes->getAgility();
    }

    public function getStamina(): int
    {
        return $this->attributes->getStamina();
    }

    public function getIntelligence(): int
    {
        return $this->attributes->getIntelligence();
    }

}
