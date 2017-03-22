<?php namespace Miner\Auth\RefreshTokens;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Cache\Repository;
use Miner\Auth\Entities\User;
use Miner\Auth\Entities\Uuid;

class RefreshTokenManager
{
    /**
     * @var Repository
     */
    private $cache;

    /**
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $refreshToken
     * @param User $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function validateRefreshToken(string $refreshToken, User $user)
    {
        $activeRefreshTokens = json_decode($this->cache->get($user->getCacheKey()));

        // if there is no array or the refresh token does not exist for this user, we have an invalid refresh token
        if(!is_array($activeRefreshTokens) || !in_array($refreshToken, $activeRefreshTokens)) {
            throw new AuthenticationException('invalid refresh token');
        }

        return true;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function addForUser(User $user): string
    {
        // generate a new unique id for our token
        $uuid = new Uuid();

        // get all refresh tokens for this user based on the users cache key
        $refreshTokens = json_decode($this->cache->get($user->getCacheKey()));
        $refreshTokens[] = $uuid->toString();

        // get rid of the oldest token to make room for the new token
        while(count($refreshTokens) >= 10) {
            array_shift($refreshTokens);
        }

        // add the new token to cache (good for one year)
        // in the future we may want to add per-token expiration
        $this->cache->put($user->getCacheKey(), json_encode($refreshTokens), 60 * 24 * 365);

        return $uuid->toString();
    }
}