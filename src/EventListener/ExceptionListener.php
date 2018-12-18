<?php

namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionListener
 * @package App\EventListener
 */
class ExceptionListener
{
    private $twigEngine;

    private $env;
    /**
     * ExceptionListener constructor.
     * @param EngineInterface $twigEngine
     */
    public function __construct(EngineInterface $twigEngine, $env)
    {
        $this->twigEngine = $twigEngine;
        $this->env = $env;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ('dev' !== $this->env) {
            $exception = $event->getException();

            if ($exception instanceof HttpExceptionInterface) {
                /** @var Response $response */
                $response = $this->twigEngine->renderResponse('pages/blackhole.html.twig', ['exception' => $exception]);

                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
            } else {

                /** @var Response $response */
                $response = new Response();
                $response->setContent(sprintf("%s with code: %s", $exception->getMessage(), $exception->getCode()));
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $event->setResponse($response);
        }

    }

}
