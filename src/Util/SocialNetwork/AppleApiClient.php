<?php

namespace App\Util\SocialNetwork;

use App\Constants;
use App\Mif;
use App\Util\Common\ErrorsCollectorTrait;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use stdClass;

/**
 * Class AppleApiClient
 * @package App\Util\SocialNetwork
 */
class AppleApiClient implements SocNetApiClientInterface
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
        $paramsForAppleId = Mif::getEnvConfig(Constants::CK_APPLE_AUTH);
        $key = $this->getKey($paramsForAppleId[Constants::CK_PATHS_TO_KEYS][Constants::CK_JWT]);
        $secretKey = $this->getSecretKey($key, $paramsForAppleId);
        $link = $this->generateLink($code, $secretKey, $paramsForAppleId);
        $curl = curl_init();
        if($curl) {
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $out = json_decode(curl_exec($curl), true);
            curl_close($curl);
        } else {
            $this->addError('error initialization curl php');
            return;
        }

        if (!isset($out['id_token'])) {
            $this->addError('error get data user');
            return;
        }

        $this->receiveUserIdByToken($out['id_token']);
    }

    /**
     * @param string $token
     */
    public function receiveUserIdByToken(string $token) : void
    {
        $paramsForAppleId = Mif::getEnvConfig(Constants::CK_APPLE_AUTH);
        $keyForDecode = $this->getKey($paramsForAppleId[Constants::CK_PATHS_TO_KEYS][Constants::CK_DECODE]);
        $convertedDataUser = $this->decode($token, $keyForDecode, $paramsForAppleId[Constants::CK_ALG_FOR_DECODE]);
        $this->userId = $convertedDataUser->sub;
        $this->userEmail = $convertedDataUser->email ?? null;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getKey(string $path) : string
    {
        $jwt = Mif::getProjectDir() . "/" . Constants::DIR_SECRET_KEY . $path;
        return file_get_contents($jwt);
    }

    /**
     * @param string $keyJWT
     * @param array $paramsForAppleId
     * @return string
     */
    private function getSecretKey(string $keyJWT, array $paramsForAppleId) : string
    {
        $expireTime = time() + 3600;
        $claims = [
            'iss' => $paramsForAppleId[Constants::CK_ISS],
            'aud' => $paramsForAppleId[Constants::CK_AUD],
            'sub' => $paramsForAppleId[Constants::CK_CLIENT_ID],
            'iat' => time(),
            'exp' => $expireTime,
        ];

        return JWT::encode(
            $claims,
            $keyJWT,
            $paramsForAppleId[Constants::CK_ALG],
            $paramsForAppleId[Constants::CK_KID]
        );
    }

    /**
     * @param string $code
     * @param string $secretKey
     * @param array $paramsForAppleId
     * @return string
     */
    private function generateLink(string $code, string $secretKey, array $paramsForAppleId) : string
    {
        $link = "{$paramsForAppleId[Constants::CK_GET_DATA_BY_LINK]}?";
        $link .= "client_id={$paramsForAppleId[Constants::CK_CLIENT_ID]}&";
        $link .= "code={$code}&";
        $link .= "client_secret={$secretKey}&";
        $link .= "grant_type={$paramsForAppleId[Constants::CK_GRANT_TYPE]}";
        return $link;
    }

    /**
     * @param string $token
     * @param string $keyForDecode
     * @param array $algDecode
     * @return stdClass
     */
    private function decode(string $token, string $keyForDecode, array $algDecode) : stdClass
    {
        $key = json_decode($keyForDecode, true);
        $jwk = JWK::parseKeySet($key);
        return JWT::decode($token, $jwk, $algDecode);
    }
}
