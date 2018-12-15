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
        $result = [];

        $options = [
            'method' => 'post',
            'action' => $this->generateUrl('search_ajax')
        ];
        $form = $this->createForm(SearchFormType::class, null, $options);
        $result['form'] = $form->createView();

        /** @var Response $response */
        $response = $this->render('pages/home.html.twig', $result);
        $response->setSharedMaxAge(0);
        $response->setPublic();

        return $response;
    }

}
