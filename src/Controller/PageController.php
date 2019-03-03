<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Forms\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;

/**
 * Class PageController
 * @package App\Controller
 */
class PageController extends AbstractController
{

    /**
     * @Route({
     *     "fr": "/contactez-nous",
     *     "en": "/contact-us",
     *     "de": "/kontaktiere-uns",
     *     "es": "/contactenos",
     *     "pt": "/contate-nos"
     * }, name="contact")
     *
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function contact(Request $request)
    {
        /** @var Router $router */
        $router = $this->get('router');

        $optionsForm = [
            'method' => 'POST',
            'action' => $router->generate('contact')
        ];

        /** @var Contact $contact */
        $contact = new Contact();
        $contactForm = $this->createForm(ContactFormType::class, $contact, $optionsForm);

        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {

        }

        $result['formContact'] = $contactForm->createView();

        /** @var Response $response */
        $response = $this->render('pages/contact.html.twig', $result);
        $response->setSharedMaxAge(3600);

        return $response;
    }

}