<?php namespace Miner\Auth\Jwt;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Miner\Auth\Entities\User;

class JwtFactory
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function __construct(string $secret = null)
    {
        $this->secret = $secret !== null ? $secret : getenv('AUTH_SECRET');
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function generateForUser(User $user): string
    {
        $builder = new Builder();

        $builder->setIssuer('http://auth.miner.com'); // Configures the issuer (iss claim)
        $builder->setAudience('http://miner.com'); // Configures the audience (aud claim)
        $builder->setIssuedAt(time()); // Configures the time that the token was issue (iat claim)
        $builder->setExpiration(time() + 3600); // Configures the expiration time of the token (nbf claim)
        $builder->set('userId', $user->getId()); // Configures a new claim, called "uid"
        $builder->set('userRole', $user->getRole());
        $builder->sign(new Sha256(), $this->secret); // creates a signature using "testing" as key

        return $builder->getToken();
    }
}