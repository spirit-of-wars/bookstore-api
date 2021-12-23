<?php

namespace App\Util\SocialNetwork;

use App\Constants;
use App\Mif;
use App\Util\Authorization\AuthConstant;
use App\Util\Common\ErrorsCollectorTrait;

/**
 * Class FbApiClient
 * @package App\Util\SocialNetwork
 */
class FbApiClient implements SocNetApiClientInterface
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
        $link = $this->getLinkForAuthFb($code, $redirectUrl);
        $curl = curl_init();
        if($curl) {
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $out = json_decode(curl_exec($curl), true);

            if (empty($out) || !isset($out['access_token'])) {
                $this->addError('error get access token');
                return;
            }
            $this->receiveUserIdByToken($out['access_token']);
        } else {
            $this->addError('error initialization curl php');
        }
    }

    /**
     * @param string $token
     */
    public function receiveUserIdByToken(string $token) : void
    {
        $link = $this->getLinkDataUser($token);
        $curl = curl_init();
        if($curl) {
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $out = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if (array_key_exists(AuthConstant::AUTH_FIELD_ERROR, $out)) {
                $this->addError($out[AuthConstant::AUTH_FIELD_ERROR]);
                return;
            }

            $this->userId = $out['id'];
            $this->userEmail = $out['email'] ?? null;
        } else {
            $this->addError('error initialization curl php');
        }
    }

    /**
     * @param string $code
     * @param string $redirectUrl
     * @return string
     */
    private function getLinkForAuthFb(string $code, string $redirectUrl) : string
    {
            $uriForQuery = Mif::getEnvConfig(Constants::CK_FACEBOOK_AUTH_ARRAY_LINK);
            $clientId = Mif::getEnvConfig(Constants::CK_FACEBOOK_AUTH_CLIENT_ID);
            $clientSecret = Mif::getEnvConfig(Constants::CK_FACEBOOK_AUTH_CLIENT_SECRET);
            return "{$uriForQuery['getAccessToken']}?client_id={$clientId}&client_secret={$clientSecret}&redirect_uri={$redirectUrl}&code={$code}";
    }

    private function getLinkDataUser(string $accessToken)
    {
        $uriForQuery = Mif::getEnvConfig(Constants::CK_FACEBOOK_AUTH_ARRAY_LINK);
        return "{$uriForQuery['getDataUser']}?access_token={$accessToken}&fields=id,name,email";
    }
}
