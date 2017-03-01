<?php namespace Miner\Auth\Repositories;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Miner\Auth\Entities\AuthenticatedUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository
{
    public function __construct()
    {
        $this->db = app('db');
    }

    public function findAuthenticatedUserByUsernameAndPassword($username, $password)
    {
        $result = $this->db->select('SELECT id, role, password_hash FROM users WHERE username = ?', [$username]);

        if(!is_array($result) || count($result) === 0) {
            throw new NotFoundHttpException();
        }

        if(count($result) > 1) {
            throw new \Exception('more than one user matched');
        }

        $user = $result[0];

        if($user->password_hash !== $password) {
            throw new AuthenticationException('invalid password');
        }

        return new AuthenticatedUser($user->id, $user->role);
    }

    public function findAuthenticatedUser($id)
    {
        $result = $this->db->select('SELECT id, role FROM users WHERE id = ?', [$id]);

        if(!is_array($result) || count($result) === 0) {
            throw new NotFoundHttpException();
        }

        $user = $result[0];

        return new AuthenticatedUser($user->id, $user->role);
    }
}