<?php

declare(strict_types=1);

namespace App\Controller\Layout;

use App\Controller\ControllerTraits\LayoutTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class ModalSocialNetwork extends AbstractController
{
    use LayoutTrait;

    /**
     * @param Request $request
     * @param string|null $paypalLink
     * @param string|null $tipeeeLink
     * @param string|null $facebookLink
     * @param string|null $twitterLink
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        ?string $paypalLink,
        ?string $tipeeeLink,
        ?string $facebookLink,
        ?string $twitterLink
    ): Response
    {
        $displayPopupPage = $this->getParameter('displayPopupPage') ?? 2;
        $popupState = $this->getParameter('popupState') ?? 'disabled';

        ['facebook' => $facebook, 'twitter' => $twitter] = $this->ctaFooter(null, $facebookLink, $twitterLink);

        $params = [
            'popupState' => $popupState,
            'displayPage' => $displayPopupPage,
            'facebook' =>  $facebook,
            'twitter' => $twitter,
            'paypal' => [
                'label' => ucfirst('paypal'),
                'path' => $paypalLink,
                'blank' => true,
                'icon_class' => 'paypal'
            ],
            'tipeee' => [
                'label' => ucfirst('tipeee'),
                'path' => $tipeeeLink,
                'blank' => true,
                'icon_class' => 'tipeee'
            ],
            'description' => null,
            'pageImage200' => '/build/images/logos/astro_otter_200-200.png'
        ];
        return $this->render('includes/components/modal_social_network.html.twig', $params);
    }
}
