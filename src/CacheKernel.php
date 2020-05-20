<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CacheKernel
 *
 * @package App
 */
class CacheKernel extends HttpCache
{
    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            'default_ttl' => 31556952
        ];
    }

    /**
     * @param Request $request
     * @param bool $catch
     *
     * @return Response
     * @throws \Exception
     */
    protected function invalidate(Request $request, $catch = false): Response
    {
        if ('PURGE' !== $request->getMethod()) {
            return parent::{__FUNCTION__}(...func_get_args());
            //return parent::invalidate($request, $catch);
        }

        if ('127.0.0.1' !== $request->getClientIp()) {
            return new Response('Invalid HTTP cache', Response::HTTP_BAD_REQUEST);
        }

        /** @var Response $response */
        $response = new Response();

        if ($this->getStore()->purge($request->getUri())) {
            $response->setStatusCode(Response::HTTP_OK, 'Purged');
        } else {
            $response->setStatusCode(Response::HTTP_NOT_FOUND, 'Not found');
        }

        return $response;
    }
}
