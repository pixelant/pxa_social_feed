<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Service\Expire;

use Pixelant\PxaSocialFeed\Domain\Model\Token;

/**
 * Class FacebookAccessTokenExpireService
 */
class FacebookAccessTokenExpireService
{
    /**
     * @var Token
     */
    protected $token;

    /**
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Check if access token is valid
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->token->isValidFacebookAccessToken() === false;
    }

    /**
     * Check if token expire soon
     *
     * @param int $soonExpireAfterDays When we assume that token expire soon
     * @return bool
     * @throws \Exception
     */
    public function willExpireSoon(int $soonExpireAfterDays): bool
    {
        return $this->expireWhen() <= $soonExpireAfterDays;
    }

    /**
     * Check how many days left for token
     *
     * @return int
     * @throws \Exception
     */
    public function expireWhen(): int
    {
        $expireAt = $this->token->getFacebookAccessTokenMetadataExpirationDate();
        if ($expireAt !== null && $expireAt->getTimestamp() >= time()) {
            $today = new \DateTime();
            $interval = $today->diff($expireAt);

            return (int)$interval->format('%a');
        }

        return 0;
    }

    /**
     * Access token require check
     *
     * @return bool
     */
    public function tokenRequireCheck(): bool
    {
        return $this->token->isFacebookType() || $this->token->isInstagramType();
    }
}
