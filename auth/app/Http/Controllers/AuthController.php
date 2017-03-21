<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Routing\Controller as BaseController;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Miner\Auth\Entities\User;
use Miner\Auth\Repositories\UserRepository;
use Miner\Auth\Entities\Uuid;

class AuthController extends BaseController
{
    public function login(Request $request, UserRepository $userRepository, Cache $cache)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $email = $request->get('email');
        $password = $request->get('password');

        $user = $userRepository->findByEmail($email);

        // use php 5.5 password_hash/password_verify magix to validate the password matches
        if(!password_verify($password, $user->getPasswordHash())) {
            throw new \Exception('invalid password');
        }

        $accessToken = (string)$this->generateAccessToken($user);

        // generate a new unique id for our token
        $uuid = new Uuid();

        // get all refresh tokens for this user based on the
        $refreshTokens = json_decode($cache->get($user->getCacheKey()));
        $refreshTokens[] = $uuid->toString();

        // get rid of the oldest token to make room for the new token
        if(count($refreshTokens) >= 10) {
            array_shift($refreshTokens);
        }

        // add the new token to cache
        $cache->set($user->getCacheKey(), json_encode($refreshTokens));

        return response()->json(['accessToken' => $accessToken, 'refreshToken' => $uuid->toString()]);
    }

    public function refresh(Request $request, UserRepository $userRepository, Cache $cache)
    {
        $this->validate($request, [
            'accessToken' => 'required',
            'refreshToken' => 'required'
        ]);

        // convert our request to a jwt token
        $token = (new Parser())->parse($request->get('accessToken'));

        // verify the password jwt token and make sure its signature checks out with its content
        // we need the token to prove that this user once authenticated using /login
        if(!$token->verify(new Sha256(), 'secret')) {
            throw new \Exception('invalid access token');
        }

        $user = $userRepository->findById($token->getClaim('userId'));

        // check for existence for this users refresh tokens
        $refreshTokens = json_decode($cache->get($user->getCacheKey()));
        if(!in_array($request->get('refreshToken'), $refreshTokens)) {
            throw new \Exception('invalid refresh token');
        }

        // generate a new access token, fuck yah!
        $accessToken = (string)$this->generateAccessToken($user);

        return response()->json(['accessToken' => $accessToken]);
    }

    private function generateAccessToken(User $user)
    {
        $token = (new Builder())->setIssuer('http://auth.miner.com') // Configures the issuer (iss claim)
            ->setAudience('http://miner.com') // Configures the audience (aud claim)
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            ->setExpiration(time() + 3600) // Configures the expiration time of the token (nbf claim)
            ->set('userId', $user->getId()) // Configures a new claim, called "uid"
            ->set('userRole', $user->getRole())
            ->sign(new Sha256(), getenv('auth_secret')) // creates a signature using "testing" as key
            ->getToken(); // Retrieves the generated token

        return $token;
    }
}
