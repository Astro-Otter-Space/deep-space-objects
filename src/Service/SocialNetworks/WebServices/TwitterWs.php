<?php


namespace App\Service\SocialNetworks\WebServices;


use Abraham\TwitterOAuth\TwitterOAuth;
use App\Entity\ES\Dso;
use App\Service\SocialNetworks\Singleton\Twitter;

/**
 * Class TwitterWs
 * @package App\Service\SocialNetworks\WebServices
 */
class TwitterWs implements socialNetworkInterface
{
    /** @var Twitter|TwitterOAuth */
    private $twitterWs;

    private $consumerKey;
    private $consumerSecretKey;
    private $accessToken;
    private $accessTokenSecret;

    /**
     * @return mixed
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * @param mixed $consumerKey
     *
     * @return TwitterWs
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConsumerSecretKey()
    {
        return $this->consumerSecretKey;
    }

    /**
     * @param mixed $consumerSecretKey
     *
     * @return TwitterWs
     */
    public function setConsumerSecretKey($consumerSecretKey)
    {
        $this->consumerSecretKey = $consumerSecretKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     *
     * @return TwitterWs
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccessTokenSecret()
    {
        return $this->accessTokenSecret;
    }

    /**
     * @param mixed $accessTokenSecret
     *
     * @return TwitterWs
     */
    public function setAccessTokenSecret($accessTokenSecret)
    {
        $this->accessTokenSecret = $accessTokenSecret;
        return $this;
    }


    /**
     * TwitterWs constructor.
     *
     * @param string $consumerKey
     * @param string $consumerSecretKey
     * @param string $accessToken
     * @param string $accessTokenSecret
     */
    public function __construct(string $consumerKey, string $consumerSecretKey, string $accessToken, string $accessTokenSecret)
    {
        $this->setConsumerKey($consumerKey);
        $this->setConsumerSecretKey($consumerSecretKey);
        $this->setAccessToken($accessToken);
        $this->setAccessTokenSecret($accessTokenSecret);

        $this->buildFactory();
    }

    /**
     * Create instance Singleton
     */
    public function buildFactory(): void
    {
        $this->twitterWs = Twitter::getInstance($this->getConsumerKey(), $this->getConsumerSecretKey(), $this->getAccessToken(), $this->getAccessTokenSecret());
    }

    /**
     * @param Dso $dso
     */
    public function postLink(Dso $dso): void
    {
        $parameters = [
            'status' => sprintf('Object of the Day : %s', $dso->getId()),
            'attachment_url' => $dso->getFullUrl()
        ];
        $tweet = $this->twitterWs->post('statuses/update', $parameters);
    }

    /**
     * @param array $body
     */
    public function buildResponse(array $body)
    {
        // TODO: Implement buildResponse() method.
    }

}
