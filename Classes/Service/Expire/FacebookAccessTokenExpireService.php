<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Service\Expire;

use Pixelant\PxaSocialFeed\Domain\Model\Token;

/**
 * Class FacebookAccessTokenExpireService
 * @package Pixelant\PxaSocialFeed\Service\Expire
 */
class FacebookAccessTokenExpireService
{
    /**
     * @var Token
     */
    protected $token = null;

    /**
     * When we assume that token expire soon
     *
     * @var int
     */
    protected $soonExpireAfterDays = 5;

    /**
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Check if accees token is valid
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->token->isValidFacebookAccessToken();
    }

    /**
     * @return bool
     */
    public function willExpireSoon(): bool
    {
        return $this->expireWhen() <= $this->soonExpireAfterDays;
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
        if ($expireAt !== null) {
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
