<?php

namespace App\Controller\Pages;

use App\Controller\LayoutController;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class LegalNotice extends AbstractController
{

    use SymfonyServicesTrait;

    /**
     * @Route({
     *     "fr": "/mentions-legales",
     *     "en": "/legal-notice",
     *     "de": "/legal-notice",
     *     "es": "/legal-notice",
     *     "pt": "/legal-notice"
     * }, name="legal_notice")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $result = [];

        $result['title'] = $this->translator->trans('legal_notice.title');
        $result['first_line'] = $this->translator->trans('legal_notice.line_first', ['%dso%' => $this->translator->trans('dso')]);
        $result['second_line'] = $this->translator->trans('legal_notice.line_sec');
        $result['host'] = [
            'name' => $this->translator->trans('legal_notice.host.name'),
            'adress' => $this->translator->trans('legal_notice.host.adress'),
            'cp' => $this->translator->trans('legal_notice.host.cp'),
            'city' => $this->translator->trans('legal_notice.host.city'),
            'country' => $this->translator->trans('legal_notice.host.country')
        ];
        $result['third_line'] = $this->translator->trans('legal_notice.contact', [
            '%url_contact%' => $this->router->generate(sprintf('contact.%s', $request->getLocale())),
            '%label_contact%' => $this->translator->trans('contact.title')
        ]);

        $response = $this->render('pages/random.html.twig', $result);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);

        return $response;
    }

}
