<?php


namespace App\Modules\Character\Application\Commands;


class CreateCharacterCommand
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $gender;
    /**
     * @var int
     */
    private $characterClassId;
    /**
     * @var string
     */
    private $userId;

    public function __construct(string $name, string $gender, int $characterClassId, string $userId)
    {
        $this->name = $name;
        $this->gender = $gender;
        $this->characterClassId = $characterClassId;
        $this->userId = $userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getCharacterClassId(): int
    {
        return $this->characterClassId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
