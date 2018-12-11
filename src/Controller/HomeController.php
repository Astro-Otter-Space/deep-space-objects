<?php

namespace App\Controller;

use App\Forms\SearchFormType;
use Astrobin\Services\GetImage;
use Astrobin\Services\GetTodayImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepage(Request $request)
    {
        $result = [];

        $options = [
            'method' => 'post',
            'action' => $this->generateUrl('search_ajax')
        ];
        $form = $this->createForm(SearchFormType::class, null, $options);
        $result['form'] = $form->createView();

        return $this->render('pages/home.html.twig', $result);
    }
}
