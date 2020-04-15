<?php


namespace App\Modules\Character\Domain;


use App\Traits\ThrowsDice;

class HitPoints
{
    use ThrowsDice;

    /**
     * @var int
     */
    private $currentHitPoints;

    /**
     * @var int
     */
    private $maximumHitPoints;

    public static function byCharacterClass(CharacterClass $characterClass): HitPoints
    {
        $maximumHitPoints = self::staminaToHitPoints($characterClass->getStamina());

        return new HitPoints($maximumHitPoints, $maximumHitPoints);
    }

    public function withIncrementedStamina(): HitPoints
    {
        return new HitPoints(
            $this->currentHitPoints,
            $this->maximumHitPoints + self::staminaToHitPoints(1)
        );
    }

    public function withUpdatedCurrentValue(int $points): HitPoints
    {
        return new HitPoints(
            $this->currentHitPoints + $points,
            $this->maximumHitPoints
        );
    }

    protected static function staminaToHitPoints(int $staminaPoints): int
    {
        return $staminaPoints * 10 + self::throwTwoDices();
    }

    public function __construct(int $currentHitPoints, int $maximumHitPoints)
    {
        $this->currentHitPoints = $currentHitPoints;
        $this->maximumHitPoints = $maximumHitPoints;
    }

    public function getCurrentHitPoints(): int
    {
        return $this->currentHitPoints;
    }

    public function getMaximumHitPoints(): int
    {
        return $this->maximumHitPoints;
    }
}
