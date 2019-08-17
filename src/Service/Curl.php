<?php

namespace App\Service;

use App\Entity\ApiUser;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function getBearerToken(ApiUser $apiUser)
    {
        $urlApiLogin = $this->router->generate('api_auth_login'); //, [], UrlGeneratorInterface::ABSOLUTE_URL);

        $options = [
            'json' => [
                'email' => $apiUser->getEmail(),
                'password' => $apiUser->getRawPassword()
            ],
            'verify_host' => false,
        ];

        // ONLY DEV
        $options = array_merge($options, ['base_uri' => 'http://172.17.0.1:80']);

        /** @var ResponseInterface $httpResponse */
        return $this->httpClient->request(self::POST_REQUEST, $urlApiLogin, $options);
    }
}
