<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Miner\Auth\Jwt\JwtFactory;
use Miner\Auth\RefreshTokens\RefreshTokenManager;
use Miner\Auth\Repositories\UserRepository;

class AuthController extends BaseController
{
    const LOGIN_VALIDATORS = ['email' => 'required', 'password' => 'required'];

    const REFRESH_VALIDATORS = ['email' => 'required', 'refreshToken' => 'required'];

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param JwtFactory $jwtFactory
     * @param RefreshTokenManager $refreshTokenManager
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(
        Request $request,
        UserRepository $userRepository,
        JwtFactory $jwtFactory,
        RefreshTokenManager $refreshTokenManager
    ): JsonResponse
    {
        $this->validate($request, self::LOGIN_VALIDATORS);

        $user = $userRepository->findByEmail($request->get('email'));

        // use php 5.5+ password_hash/password_verify magix to validate the password matches
        if(!password_verify($request->get('password'), $user->getPasswordHash())) {
            throw new AuthenticationException('invalid password');
        }

        $accessTokenString = (string)$jwtFactory->generateForUser($user);

        $refreshToken = $refreshTokenManager->addForUser($user);

        return response()->json(['accessToken' => $accessTokenString, 'refreshToken' => $refreshToken]);
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     *
     * @return JsonResponse
     */
    public function refresh(Request $request, UserRepository $userRepository, JwtFactory $jwtFactory, RefreshTokenManager $refreshTokenManager)
    {
        $this->validate($request, self::REFRESH_VALIDATORS);

        $user = $userRepository->findByEmail($request->get('email'));

        $refreshTokenManager->validateRefreshToken($request->get('refreshToken'), $user);

        $accessTokenString = (string)$jwtFactory->generateForUser($user);

        return response()->json(['accessToken' => $accessTokenString]);
    }
}
