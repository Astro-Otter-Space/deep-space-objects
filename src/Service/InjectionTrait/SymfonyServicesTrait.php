<?php

namespace App\Service\InjectionTrait;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

trait SymfonyServicesTrait
{
    protected RouterInterface $router;
    protected TranslatorInterface $translator;

    /**
     * @required
     * @param RouterInterface $router
     *
     * @return void
     */
    public function injectRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @required
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function injectTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
