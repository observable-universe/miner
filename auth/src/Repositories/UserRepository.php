<?php namespace Miner\Auth\Repositories;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\DatabaseManager;
use Miner\Auth\Entities\User;

class UserRepository
{
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $email
     * @return User
     *
     * @throws \Exception
     */
    public function findByEmail(string $email): User
    {
        $result = $this->db->select('SELECT id, password_hash, role FROM users WHERE email = ?', [$email]);

        if(!is_array($result) || !count($result)) {
            throw new AuthenticationException("user not found ($email)");
        }

        if(count($result) > 1) {
            throw new \Exception("more than one user matched ($email)");
        }

        $user = $result[0];

        return new User($user->id, $user->password_hash, $user->role);
    }
}