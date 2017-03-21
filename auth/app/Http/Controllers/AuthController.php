<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Miner\Auth\Entities\User;
use Miner\Auth\Jwt\JwtFactory;
use Miner\Auth\RefreshTokens\RefreshTokenManager;
use Miner\Auth\Repositories\UserRepository;

class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param JwtFactory $jwtFactory
     * @param RefreshTokenManager $refreshTokenManager
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function login(
        Request $request,
        UserRepository $userRepository,
        JwtFactory $jwtFactory,
        RefreshTokenManager $refreshTokenManager
    ): JsonResponse
    {
        $user = $this->getUser($request, $userRepository);

        // use php 5.5+ password_hash/password_verify magix to validate the password matches
        if(!password_verify($request->get('password'), $user->getPasswordHash())) {
            throw new \Exception('invalid password');
        }

        $accessToken = $jwtFactory->generateForUser($user);

        $refreshToken = $refreshTokenManager->addForUser($user);

        return response()->json(['accessToken' => $accessToken, 'refreshToken' => $refreshToken]);
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param RefreshTokenManager $refreshTokenManager
     *
     * @return JsonResponse
     */
    public function refresh(Request $request, UserRepository $userRepository, RefreshTokenManager $refreshTokenManager)
    {
        $user = $this->getUser($request, $userRepository);

        $accessToken = $refreshTokenManager->addForUser($user);

        return response()->json(['accessToken' => $accessToken]);
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     *
     * @return User
     */
    private function getUser(Request $request, UserRepository $userRepository): User
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        return $userRepository->findByEmail($request->get('email'));
    }
}
