<?php


namespace App\Service\SocialNetworks\Singleton;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class Twitter
 * @package App\Service\SocialNetworks\Singleton
 */
final class Twitter extends abstractSocialNetworks
{

    public static $_instance = null;

    /**
     * @param string $consumerKey
     * @param string $consumerSecretKey
     * @param string $accessToken
     * @param string $accessTokenSecret
     *
     * @return TwitterOAuth|null
     */
    public static function getInstance(string $consumerKey, string  $consumerSecretKey, string $accessToken, string $accessTokenSecret)
    {
        if (is_null(self::$_instance)) {
            /** @var TwitterOAuth _instance */
            self::$_instance = new TwitterOAuth($consumerKey, $consumerSecretKey, $accessToken, $accessTokenSecret);
        }
        return self::$_instance;
    }

}
