<?php

namespace App\Util\SocialNetwork;

use App\Constants;
use App\Mif;
use App\Util\Authorization\AuthConstant;
use App\Util\Common\ErrorsCollectorTrait;

/**
 * Class VkApiClient
 * @package App\Util\SocialNetwork
 */
class VkApiClient implements SocNetApiClientInterface
{
    use ErrorsCollectorTrait;

    private ?string $userEmail;
    private string $userId;

    /**
     * @return string|null
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $code
     * @param string|null $redirectUrl
     */
    public function requestForUserData(string $code, ?string $redirectUrl = null) : void
    {
        $link = $this->getLinkForApiQuery($code, $redirectUrl);
        $curl = curl_init();
        if($curl) {
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $out = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if (array_key_exists(AuthConstant::AUTH_FIELD_ERROR, $out)) {
                $this->addError($out[AuthConstant::VK_AUTH_FIELD_ERROR_DESCRIPTION]);
                return;
            }

            $this->userEmail = $out[AuthConstant::USER_AUTH_FIELD_EMAIL] ?? null;
            $this->userId = $out[AuthConstant::AUTH_FIELD_USER_ID];
        } else {
            $this->addError('error initialization curl php');
        }
    }

    /**
     * @param string $code
     * @param string $redirectUrl
     * @return string
     */
    private function getLinkForApiQuery(string $code, string $redirectUrl) : string
    {
        $uriForQuery = Mif::getEnvConfig(Constants::CK_VK_AUTH_ARRAY_LINK);
        $clientId = Mif::getEnvConfig(Constants::CK_VK_AUTH_CLIENT_ID);
        $clientSecret = Mif::getEnvConfig(Constants::CK_VK_AUTH_CLIENT_SECRET);
        return "{$uriForQuery['getDataUser']}?client_id={$clientId}&client_secret={$clientSecret}&redirect_uri={$redirectUrl}&code={$code}";
    }

    /**
     * @param string $token
     */
    public function receiveUserIdByToken(string $token) : void
    {
        $uriForQuery = Mif::getEnvConfig(Constants::CK_VK_AUTH_ARRAY_LINK);
        $link = "{$uriForQuery['getIdUser']}?access_token={$token}&v=5.122";
        $curl = curl_init();
        if($curl) {
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $out = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if (array_key_exists(AuthConstant::AUTH_FIELD_ERROR, $out)) {
                $this->addError($out[AuthConstant::VK_AUTH_FIELD_ERROR_DESCRIPTION]);
                return;
            }

            $this->userEmail = null;
            $this->userId = $out['response']['id'];
        } else {
            $this->addError('error initialization curl php');
        }
    }
}
