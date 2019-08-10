<?php

namespace App\Service;

use App\Entity\ApiUser;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class Curl
 *
 * @package App\Service
 */
class Curl
{
    /** @var HttpClientInterface  */
    private $httpClient;

    /** @var RouterInterface */
    private $router;

    const GET_REQUEST = 'GET';

    const POST_REQUEST = 'POST';

    /**
     * Curl constructor.
     *
     * @param HttpClientInterface $httpClient
     * @param RouterInterface $router
     */
    public function __construct(HttpClientInterface $httpClient, RouterInterface $router)
    {
        $this->httpClient = $httpClient;
        $this->router = $router;
    }

    /**
     * @param ApiUser $apiUser
     *
     * @throws TransportExceptionInterface
     */
    public function getBearerToken(ApiUser $apiUser)
    {
        $urlApiLogin = $this->router->generate('api_auth_login');

        /** @var ResponseInterface $httpResponse */
        $httpResponse = $this->httpClient->request(self::GET_REQUEST, $urlApiLogin, [
            "username" => $apiUser->getEmail(),
            "password" => $apiUser->getRawPassword()
        ]);

        dump($httpResponse);
    }
}
