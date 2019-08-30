<?php

namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @param string $env
     */
    public function __construct(EngineInterface $twigEngine, $env)
    {
        $this->twigEngine = $twigEngine;
        $this->env = $env;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();

        if ("dev" !== $this->env) {
            if ($exception instanceof HttpExceptionInterface || $exception instanceof NotFoundHttpException) {
                /** @var Response $response */
                $response = $this->twigEngine->renderResponse('exceptions/exceptions.html.twig', ['exception' => $exception, 'env' => $this->env]);
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
