<?php


namespace App\Service\SocialNetworks\Singleton;

use Facebook\Exceptions\FacebookSDKException;

/**
 * Class Facebook
 *
 * @package App\Service\SocialNetworks\Singleton
 */
final class Facebook extends abstractSocialNetworks
{

    const API_VERSION = 'v5.0';

    /** @var \Facebook\Facebook */
    private static $_instance = null;

    /**
     * @param $appId
     * @param $appSecret
     *
     * @return \Facebook\Facebook
     * @throws FacebookSDKException
     */
    public static function getInstance($appId, $appSecret): \Facebook\Facebook
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new \Facebook\Facebook([
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'default_graph_version' => self::API_VERSION
            ]);
        }

        return self::$_instance;
    }
}
