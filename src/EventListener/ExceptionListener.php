<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ExceptionListener
 * @package App\EventListener
 */
final class ExceptionListener
{
    /** @var Environment  */
    private $twigEngine;

    /** @var string  */
    private $env;


    /**
     * ExceptionListener constructor.
     *
     * @param Environment $twigEngine
     * @param string $env
     */
    public function __construct(Environment $twigEngine, $env)
    {
        $this->twigEngine = $twigEngine;
        $this->env = $env;
    }

    /**
     * @param ExceptionEvent $event
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();

        if ("dev" !== $this->env) {
            if ($exception instanceof HttpExceptionInterface || $exception instanceof NotFoundHttpException) {
                $template = $this->twigEngine->render('exceptions/exceptions.html.twig', ['exception' => $exception, 'env' => $this->env]);

                /** @var Response $response */
                $response = new Response($template);
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
