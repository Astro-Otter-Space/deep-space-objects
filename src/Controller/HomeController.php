<?php

namespace App\Controller;

use App\Forms\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepage()
    {
        /** @var Response $response */
        $response = $this->render('pages/home.html.twig', []);
        $response->setSharedMaxAge(15);

        return $response;
    }


    /**
     * @ Route({
     *     "fr": "/contactez-nous",
     *     "en": "/contact-us"
     * }, name="contact")
     *
     * @return Response
     */
    public function contact()
    {
        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);

        return $response;
    }
}
