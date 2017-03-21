<?php namespace Miner\Auth\Repositories;

use Miner\Auth\Entities\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository
{
    public function __construct()
    {
        $this->db = app('db');
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

        if(!is_array($result) || count($result) === 0) {
            throw new NotFoundHttpException();
        }

        if(count($result) > 1) {
            throw new \Exception('more than one user matched');
        }

        return $this->createUserFromResult($result);
    }

    /**
     * @param array $result
     *
     * @return User
     */
    private function createUserFromResult(array $result): User
    {
        $user = $result[0];

        return new User($user->id, $user->password_hash, $user->role);
    }
}