<?php

namespace App\Controller;

use App\Entity\Dso;
use App\Repository\DsoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SearchController
 * @package App\Controller
 */
class SearchController extends AbstractController
{

    /**
     * @Route(
     *     "/_search",
     *     options={"expose"=true},
     *     name="search_ajax"
     * )
     *
     * @param Request $request
     * @param  DsoRepository $dsoRepository
     * @return JsonResponse
     */
    public function searchAjax(Request $request, DsoRepository $dsoRepository, TranslatorInterface $translatorInterface)
    {
        $data = [];
        if ($request->query->has('q')) {
            $searchTerm = $request->query->get('q');

            $result = $dsoRepository->gteObjectsBySearchTerms($searchTerm);

            $data = call_user_func("array_merge", array_map(function(Dso $dso) use ($translatorInterface) {
                return [
                    "id" => $dso->getId(),
                    "value" => (!empty($dso->getAlt())) ? $dso->getAlt() : $dso->getId(),
                    "description" => $translatorInterface->trans('type.' . $dso->getType()) . ' - ' . $dso->getConstId()
                ];
            }, $result));
        }

        /** @var JsonResponse $response */
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        return $response;
    }

}
