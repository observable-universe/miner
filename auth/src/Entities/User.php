<?php namespace Miner\Auth\Entities;

class User implements \JsonSerializable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var string
     */
    private $role;

    /**
     * @param int $id
     * @param string $passwordHash
     * @param string $role
     */
    public function __construct(int $id, string $passwordHash, string $role)
    {
        $this->id = $id;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return 'user-' . $this->getId();
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'role' => $this->getRole()
        ];
    }
}