<?php namespace Miner\Auth\Repositories;

use Miner\Auth\Entities\AuthenticatedUser;
use Miner\Auth\Entities\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository
{
    public function __construct()
    {
        $this->db = app('db');
    }

    public function findByEmail($email): User
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

    public function findById($id): User
    {
        $result = $this->db->select('SELECT id, password_hash, role FROM users WHERE id = ?', [$id]);

        if(!is_array($result) || count($result) === 0) {
            throw new NotFoundHttpException();
        }

        return $this->createUserFromResult($result);
    }

    private function createUserFromResult($result): User
    {
        $user = $result[0];

        return new User($user->id, $user->password_hash, $user->role);
    }
}