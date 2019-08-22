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
 * @uses NOT USED
 * @package App\Service
 */
class CurlService
{
    /** @var HttpClientInterface  */
    private $httpClient;

    /** @var RouterInterface */
    private $router;

    /** @var  */
    private $env;

    const GET_REQUEST = 'GET';

    const POST_REQUEST = 'POST';

    /**
     * CurlService constructor.
     *
     * @param HttpClientInterface $httpClient
     * @param RouterInterface $router
     * @param string $env
     */
    public function __construct(HttpClientInterface $httpClient, RouterInterface $router, $env = '')
    {
        $this->httpClient = $httpClient;
        $this->router = $router;
        $this->env = $env;
    }

    /**
     * @param ApiUser $apiUser
     *
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function getBearerToken(ApiUser $apiUser)
    {
//        $urlApiLogin = $this->router->generate('api_auth_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
//
//        $options = [
//            'json' => [
//                'email' => $apiUser->getEmail(),
//                'password' => $apiUser->getRawPassword()
//            ],
//        ];
//
//        // ONLY DEV
//        if ('dev' === $this->env) {
////            $urlApiLogin = str_replace($urlApiLogin, 'otter-in-space.local', 'http://nginx');
//        }
//        /** @var ResponseInterface $httpResponse */
//        return $this->httpClient->request(self::POST_REQUEST, $urlApiLogin, $options);
        return true;
    }
}
