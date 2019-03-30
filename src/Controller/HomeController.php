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
     * Homepage
     *
     * @Route("/", name="homepage")
     * @return Response
     */
    public function homepage()
    {
        /** @var Response $response */
        $response = $this->render('pages/home.html.twig', []);
        $response->setSharedMaxAge(3600);
        $response->setPublic();

        return $response;
    }

    /**
     * @Route("/phpinfo", name="phpinfo")
     */
    public function phpinfo()
    {
        echo phpinfo();
    }
}
