<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Miner\Auth\Repositories\UserRepository;
use Miner\Auth\Entities\Uuid;

class AuthController extends BaseController
{
    public function login(Request $request, UserRepository $userRepository)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->get('username');
        $password = $request->get('password');

        $authenticatedUser = $userRepository->findAuthenticatedUserByUsernameAndPassword($username, $password);

        $accessToken = (string)$this->generateAccessToken($authenticatedUser->getUserId(), $authenticatedUser->getRole());

        $cache = app('redis');

        $uuid = new Uuid();
        $key = 'user-' . $authenticatedUser->getUserId();
        $refreshTokens = json_decode($cache->get($key));
        $refreshTokens[] = $uuid->toString();

        if(count($refreshTokens) >= 10) {
            array_shift($refreshTokens);
        }

        $cache->set($key, json_encode($refreshTokens));

        return response()->json(['accessToken' => $accessToken, 'refreshToken' => $uuid->toString()]);
    }

    public function refresh(Request $request, UserRepository $userRepository)
    {
        $this->validate($request, [
            'accessToken' => 'required',
            'refreshToken' => 'required'
        ]);

        $token = (new Parser())->parse($request->get('accessToken'));

        if(!$token->verify(new Sha256(), 'secret')) {
            throw new \Exception('invalid access token');
        }

        $authenticatedUser = $userRepository->findAuthenticatedUser($token->getClaim('userId'));

        $cache = app('redis');
        $key = 'user-' . $token->getClaim('userId');

        $refreshTokens = json_decode($cache->get($key));

        if(!in_array($request->get('refreshToken'), $refreshTokens)) {
            throw new \Exception('invalid refresh token');
        }

        $accessToken = (string)$this->generateAccessToken($authenticatedUser->getUserId(), $authenticatedUser->getRole());

        return response()->json(['accessToken' => $accessToken]);
    }

    private function generateAccessToken($userId, $role)
    {
        $token = (new Builder())->setIssuer('http://miner.com') // Configures the issuer (iss claim)
            ->setAudience('http://client.miner.com') // Configures the audience (aud claim)
            ->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            ->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
            ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
            ->set('userId', $userId) // Configures a new claim, called "uid"
            ->set('role', $role)
            ->sign(new Sha256(), 'secret') // creates a signature using "testing" as key
            ->getToken(); // Retrieves the generated token

        return $token;
    }
}
