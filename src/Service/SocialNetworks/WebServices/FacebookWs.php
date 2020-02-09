<?php

namespace App\Service\SocialNetworks\WebServices;

use App\Entity\SocialNetworks\FacebookPost;
use App\Entity\ES\AbstractEntity;
use App\Service\SocialNetworks\Singleton\Facebook;
use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookResponse;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use Symfony\Component\HttpFoundation\Response;
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

    /** @var  */
    private $appAccessToken;

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
     * @return mixed
     */
    public function getAppAccessToken()
    {
        return $this->appAccessToken;
    }

    /**
     * @param mixed $appAccessToken
     *
     * @return FacebookWs
     */
    public function setAppAccessToken($appAccessToken)
    {
        $this->appAccessToken = $appAccessToken;
        return $this;
    }


    /**
     * FacebookWs constructor.
     *
     * @param string $appId
     * @param string $appSecret
     * @param string $pageId
     *
     * @throws FacebookSDKException
     */
    public function __construct(string $appId, string $appSecret, string $pageId)
    {
        dump(__METHOD__, $appId);
        $this->setAppId($appId)->setAppSecret($appSecret)->setPageId($pageId);
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
        $this->setAccessToken($this->facebookWs->getApp()->getAccessToken());
        $this->facebookWs->setDefaultAccessToken($this->getAccessToken());
    }

    /**
     */
    private function getPageAccessToken()
    {
        /**
         * STEP 1 : authentication
         */
        /** @var FacebookRedirectLoginHelper $helper */
        $helper = $this->facebookWs->getRedirectLoginHelper();
        try {
            /** @var AccessToken $accessToken */
            $accessToken = $helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            dump($e->getMessage());
            exit;

        } catch (FacebookSDKException $e) {
            dump($e->getMessage());
            exit;
        }

        if ($accessToken) {
            /**
             * Step 2 : get access token from oAuth
             */

            /** @var OAuth2Client $client */
            $oAuth2Client = $this->facebookWs->getOAuth2Client();
            try {
                /** @var AccessToken $accessToken */
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
//                $response = $this->facebookWs->get('/me/accounts', $accessToken);

//                dump($response);
//                die();
//                $this->setAppAccessToken($value);
            } catch (FacebookSDKException $e) {
                dump($e->getMessage());
                exit;
            }
        } else {
            dump($helper->getError(), $helper->getErrorDescription(), $helper->getErrorReason());
        }
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
            dump($fbResponse);
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
        //$this->getPageAccessToken();
        //die();
        try {
            $fbResponse = $this->facebookWs->post($this->endpoint(), [
                'message' => 'Test publication ' . $publishDate->format('Y-m-d H:i:s'),
                "link" => "https://astro-otter.space/catalog/m42--orion-nebula",
                "picture" => "https://cdn.astrobin.com/thumbs/vDiHXOHAK_fs_1824x0_kWXURFLk.jpg",
                "name" => "Title",
                "caption" => "www.example.com",
                "description" => "Description example"
            ], $this->getAccessToken());
            dump($fbResponse);
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
