<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Set locale into session
 * Class LocaleSubscriber
 * @package App\EventSubscriber
 */
class LocaleSubscriber implements EventSubscriberInterface
{

    /**
     * LocaleSubscriber constructor.
     *
     * @param string $defaultLocale
     */
    public function __construct(
        private string $defaultLocale = 'en'
    ) { }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        //$path = $request->getPathInfo();
        //$pathParts = explode('/', trim($path, '/'));
        
        if ($request->headers->has("Accept-Language")) {
            $locale = $request->headers->get('Accept-Language');
            $request->setLocale($locale);
        } else {
            if (!$request->hasPreviousSession()) {
                return;
            }  
        
        
            // try to see if the locale has been set as a _locale routing parameter
            if ($locale = $request->attributes->get('_locale')) {
                $request->getSession()->set('_locale', $locale);
            } else {
                // if no explicit locale has been set on this request, use one from the session
	        $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 17]
            ]
        ];
    }
}
