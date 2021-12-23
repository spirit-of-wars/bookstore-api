<?php

namespace App\Util\Authorization;

/**
 * Class AuthConstant
 * @package App\Util\Authentication
 */
class AuthConstant
{
    const NEIGHBOR_AUTHENTICATION_PARAMETER = 'auth';
    const NEIGHBOR_DATA_PARAMETER = 'data';
    const NEIGHBOR_PUBLIC_PARAMETER = 'pub';
    const USER_AUTH_FIELD_EMAIL = 'email';
    const USER_AUTH_FIELD_CONFIRM_TOKEN = 'token';
    const USER_AUTH_FIELD_CONFIRM_CODE = 'code';
    const USER_AUTH_HEADER_ACCESS_TOKEN = 'Access-Token';
    const USER_AUTH_FIELD_ACCESS_TOKEN = 'accessToken';
    const USER_AUTH_FIELD_REFRESH_TOKEN = 'refreshToken';
    const USER_AUTH_FIELD_CODE = 'code';
    const USER_AUTH_FIELD_REDIRECT_URI = 'redirectUri';
    const USER_SOC_AUTH_HASH = 'socAuthHash';

    /**
     * Social network output
     */
    const AUTH_FIELD_ERROR = 'error';
    const AUTH_FIELD_USER_ID = 'user_id';

    //vk output params
    const VK_AUTH_FIELD_ERROR_DESCRIPTION = 'error_description';
}
