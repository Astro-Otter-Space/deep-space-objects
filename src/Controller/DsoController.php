<?php


namespace App\Controller;

use App\Entity\Dso;
use App\Managers\DsoManager;
use Astrobin\Response\Image;
use Astrobin\Services\GetImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class DsoController
 * @package App\Controller
 */
class DsoController extends AbstractController
{

    /**
     * @Route({
     *  "en": "/catalog/{id}",
     *  "fr": "/catalogue/{id}",
     *  "pt": "/catalogo/{id}"
     * }, name="dso_show")
     *
     * @param string $id
     * @param DsoManager $dsoManager
     * @return Response
     * @throws \Astrobin\Exceptions\WsException
     * @throws \Astrobin\Exceptions\WsResponseException
     * @throws \ReflectionException
     */
    public function show(string $id, DsoManager $dsoManager)
    {
        $params = [];

        /** @var Dso $dso */
        $dso = $dsoManager->buildDso($id);

        if (!is_null($dso)) {
            /** @var GetImage $astrobinWs */
            $astrobinWs = new GetImage();
            $listImages = $astrobinWs->getImagesBySubject($dso->getId(), 5);

            $params['dso'] = $dsoManager->formatVueData($dso);
            if (0 < $listImages->count) {
                $params['images'] = array_map(function (Image $image) {
                    return $image->url_regular;
                }, iterator_to_array($listImages));
            }
        }

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);

        return $this->render('pages/dso.html.twig', $params, $response);
    }

}
