<?php


namespace App\Service\SocialNetworks\Singleton;

/**
 * Class Facebook
 * @package App\Service\SocialNetworks\Singleton
 */
final class Facebook extends abstractSocialNetworks
{

    /** @var null */
    private static $_instance = null;

    /**
     * @param $appId
     * @param $appSecret
     *
     * @return Facebook
     */
    public static function getInstance($appId, $appSecret): self
    {
        if (is_null(self::$_instance)) {
            self::$_instance = '';
        }

        return self::$_instance;
    }
}
