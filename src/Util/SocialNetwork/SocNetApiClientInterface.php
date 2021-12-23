<?php

namespace App\Util\SocialNetwork;

use App\Interfaces\ErrorsCollectorInterface;

/**
 * Interface SocNetApiClientInterface
 * @package App\Util\SocialNetwork
 */
interface SocNetApiClientInterface extends ErrorsCollectorInterface
{
    /**
     * @param string $code
     * @param string|null $redirectUrl
     * @return void
     */
    public function requestForUserData(string $code, ?string $redirectUrl = null) : void;

    /**
     * @return string|null
     */
    public function getUserEmail();

    /**
     * @return string
     */
    public function getUserId();

    /**
     * @param string $token
     */
    public function receiveUserIdByToken(string $token) : void;
}
