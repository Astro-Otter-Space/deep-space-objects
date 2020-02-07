<?php

namespace App\Service\SocialNetworks\WebServices;

use App\Entity\DTO\FacebookPost;
use App\Entity\ES\AbstractEntity;
use App\Service\SocialNetworks\Singleton\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\ResponseCacheStrategy;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class FacebookWs
 * @package App\Service\SocialNetworks\WebServices
 *
 * Doc :
 * POST : https://developers.facebook.com/docs/graph-api/reference/v6.0/post
 * Feed : https://developers.facebook.com/docs/graph-api/reference/v6.0/page/feed#publish
 */
class FacebookWs implements socialNetworkInterface
{
    /** @var Facebook|\Facebook\Facebook */
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
     *
     * @throws FacebookSDKException
     */
    public function __construct()
    {
        $this->buildFactory();
    }


    /**
     * Build Instance Facebook
     *
     * @throws FacebookSDKException
     */
    public function buildFactory(): void
    {
        $this->facebookWs = Facebook::getInstance($this->getAppId(), $this->getAppSecret());
    }

    /**
     * Get last facebook post
     * @return FacebookPost
     */
    public function getPost():? FacebookPost
    {
        try {
            /** @var FacebookResponse $fbResponse */
            $fbResponse = $this->facebookWs->get($this->endpoint(), $this->getAccessToken());

            if (Response::HTTP_OK === $fbResponse->getHttpStatusCode()) {
                /** @var FacebookPost $facebookPost */
                $facebookPost = $this->buildResponse($fbResponse->getBody());

                return $facebookPost;
            }
        } catch (FacebookResponseException $e) {
            dump($e->getMessage());

        } catch (FacebookSDKException $e) {
            dump($e->getMessage());
        }

        return null;
    }


    /**
     * @param AbstractEntity $object
     *
     * @return FacebookResponse
     * @throws FacebookSDKException
     */
    public function sendPost(?AbstractEntity $object)
    {
        /** @var \DateTimeInterface $publishDate */
        $publishDate = new \DateTime();

        try {
            $fbResponse = $this->facebookWs->post($this->endpoint(), [
                'message' => 'Test publication ' . $publishDate->format('Y-m-d H:i:s'),
            ]);

            return $fbResponse;
        } catch (FacebookResponseException $e) {
            dump($e->getMessage());
        }

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
            $facebookPost = $objectNormalizer->denormalize(reset($body['data']), FacebookPost::class);

            return $facebookPost;
        }
    }


    /**
     * @return string
     */
    private function endpoint()
    {
        return sprintf('/%s/feed', $this->getPageId());
    }

}
