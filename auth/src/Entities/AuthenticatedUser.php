<?php namespace Miner\Auth\Entities;

class AuthenticatedUser implements \JsonSerializable
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $role;

    /**
     * @param int $userId
     * @param string $role
     */
    public function __construct(int $userId, string $role)
    {
        $this->userId = $userId;
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'userId' => $this->getUserId(),
            'role' => $this->getRole()
        ];
    }
}