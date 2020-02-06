<?php

namespace App\Service\SocialNetworks\WebServices;

use App\Entity\DTO\FacebookPost;
use App\Entity\ES\AbstractEntity;
use App\Service\SocialNetworks\Singleton\Facebook;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class FacebookWs
 * @package App\Service\SocialNetworks\WebServices
 */
class FacebookWs implements socialNetworkInterface
{
    /** @var Facebook */
    private $facebookWs;

    /** @var string */
    private $appId;
    /** @var string */
    private $appSecret;
    /** @var string */
    private $accessToken;
    /** @var string */
    private $pageId;

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     *
     * @return FacebookWs
     */
    public function setAppId(string $appId): FacebookWs
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     *
     * @return FacebookWs
     */
    public function setAppSecret(string $appSecret): FacebookWs
    {
        $this->appSecret = $appSecret;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     *
     * @return FacebookWs
     */
    public function setAccessToken(string $accessToken): FacebookWs
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageId(): string
    {
        return $this->pageId;
    }

    /**
     * @param string $pageId
     *
     * @return FacebookWs
     */
    public function setPageId(string $pageId): FacebookWs
    {
        $this->pageId = $pageId;
        return $this;
    }


    /**
     * FacebookWs constructor.
     */
    public function __construct()
    {
        $this->buildFactory();
    }


    /**
     * Build Instance Facebook
     */
    public function buildFactory(): void
    {
        $this->facebookWs = Facebook::getInstance($this->getAppId(), $this->getAppSecret());
    }

    /**
     * @return FacebookPost
     */
    public function getPost(): FacebookPost
    {
        // TODO send request
    }


    /**
     * @param AbstractEntity $object
     */
    public function sendPost(AbstractEntity $object)
    {

    }

    /**
     * Transform response from facebook into DTo FacebookPost
     * @inheritDoc
     */
    public function buildResponse(array $body): FacebookPost
    {
        /** @var ObjectNormalizer $objectNormalizer */
        $objectNormalizer = new ObjectNormalizer();

        if (array_key_exists('data', $body) && 1 === count($body['data'])) {
            /** @var FacebookPost $facebookPost */
            $facebookPost = $objectNormalizer->denormalize(reset($body['data']), FacebookPost::class));

            return $facebookPost;
        }
    }
}
