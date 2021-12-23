<?php

namespace App;

/**
 * Class Constants
 * @package App
 */
class Constants
{
    /**
     * Default maximum number for elements on page
     */
    const PAGE_LIMIT = 50;

    /**
     * Default sort order for list queries
     */
    const DEFAULT_SORT_ORDER = 'desc';

    /**
     * Default lifetime for access token in minutes
     */
    const DEFAULT_LIFETIME_ACCESS_TOKEN = 5;

    /**
     * Default lifetime for refresh token in minutes
     */
    const DEFAULT_LIFETIME_REFRESH_TOKEN = 24 * 60;

    /**
     * Default lifetime for confirm link in minutes
     */
    const DEFAULT_LIFETIME_ACTIVATE_LINK = 24 * 60;
    const GUEST_EMAIL = 'guest@guest.loc';

    const REQUEST_ERROR_PARAMETER_REQUIRED = 460;
    const REQUEST_ERROR_PARAMETER_WRONG_TYPE = 461;
    const REQUEST_ERROR_PARAMETER_CONSTRAINT = 462;
    const DIR_SECRET_KEY = 'Unversion';

    const EMAIL_SEND_RABBIT = 'rabbit';
    const EMAIL_SEND_FILE = 'file';
    const EMAIL_SEND_BOTH = 'file_n_rabbit';
    const EMAIL_SEND_STRAIGHT = 'straight';

    /*******************************************************************************************************************
     * Configuration keys (prefix CONFIG_KEY_ -> CK_)
     ******************************************************************************************************************/
    const CK_APP_ENV = 'APP_ENV';
    const CK_LIFETIME_ACCESS_TOKEN = 'LIFETIME_ACCESS_TOKEN';
    const CK_LIFETIME_REFRESH_TOKEN = 'LIFETIME_REFRESH_TOKEN';
    const CK_LIFETIME_ACTIVATE_LINK = 'LIFETIME_ACTIVATE_LINK';

    /**
     * all (save data in rabbit and file)
     * rabbit (save data in rabbit)
     * file (save data in file)
     */
    const CK_EMAIL_SEND_MODE = 'EMAIL_SEND_MODE';
    const CK_MIF_SENDER_EMAIL = 'MIF_SENDER_EMAIL';

    const CK_VK_AUTH_ARRAY_LINK = 'VK_AUTH_ARRAY_LINK';
    const CK_VK_AUTH_CLIENT_ID = 'VK_AUTH_CLIENT_ID';
    const CK_VK_AUTH_CLIENT_SECRET = 'VK_AUTH_CLIENT_SECRET';

    const CK_FACEBOOK_AUTH_ARRAY_LINK = 'FACEBOOK_AUTH_ARRAY_LINK';
    const CK_FACEBOOK_AUTH_CLIENT_ID = 'FACEBOOK_AUTH_CLIENT_ID';
    const CK_FACEBOOK_AUTH_CLIENT_SECRET = 'FACEBOOK_AUTH_CLIENT_SECRET';

    const PUBLIC_DIRECTORY = '/public';

    /**
     * Configuration file folders
     */
    const DEFAULT_FILE_FOLDER = '/files';

    /**
     * params for login with apple id
     */
    const CK_APPLE_AUTH = 'APPLE_AUTH';
    const CK_PATHS_TO_KEYS = 'PATHS_TO_KEYS';
    const CK_JWT = 'JWT';
    const CK_DECODE = 'DECODE';
    const CK_ISS = 'ISS';
    const CK_AUD = 'AUD';
    const CK_CLIENT_ID = 'CLIENT_ID';
    const CK_ALG = 'ALG';
    const CK_KID = 'KID';
    const CK_ALG_FOR_DECODE = 'ALG_FOR_DECODE';
    const CK_GET_DATA_BY_LINK = 'GET_DATA_BY_LINK';
    const CK_GRANT_TYPE = 'GRANT_TYPE';
}
