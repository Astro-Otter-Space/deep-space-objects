<?php

declare(strict_types=1);

namespace App\Controller\Layout;

use App\Controller\ControllerTraits\LayoutTrait;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Footer extends AbstractController
{
    use SymfonyServicesTrait, LayoutTrait;

    /**
     * @param Request $request
     * @param string|null $githubLink
     * @param string|null $paypalLink
     * @param string|null $facebookLink
     * @param string|null $twitterLink
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        ?string $githubLink,
        ?string $paypalLink,
        ?string $facebookLink,
        ?string $twitterLink
    ): Response
    {
        /** @var Request $mainRequest */
        $mainRequest = $this->get('request_stack')->getMainRequest();
        $mainRoute = $mainRequest->get('_route');

        $result['share'] = $this->ctaFooter($githubLink, $facebookLink, $twitterLink);

        $result['links_footer'] = [
            'api' => [
                'label' => 'API',
                'path' => $this->router->generate(sprintf('help_api_page.%s', $request->getLocale()))
            ],
            'legal_notice' => [
                'label' => $this->translator->trans('legal_notice.title'),
                'path' => $this->router->generate(sprintf('legal_notice.%s', $request->getLocale())),
            ],
            'contact' => [
                'label' => $this->translator->trans('contact.title'),
                'path' => $this->router->generate(sprintf('contact.%s', $request->getLocale())),
            ],
            'support' => [
                'label' => $this->translator->trans('support.title'),
                'path' => $this->router->generate(sprintf('help_astro-otter.%s', $request->getLocale())),
            ]
        ];

        $result['main_route'] = $mainRoute;

        $response = new Response();
        $response->setSharedMaxAge(0);

        return $this->render('includes/layout/footer.html.twig', $result, $response);
    }

}
