<?php

namespace App\Controller\Dso;

use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\DsoDataTransformer;
use App\Managers\DsoManager;
use App\Repository\AbstractRepository;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ByConstellationIdAjax extends AbstractController
{
    use DsoTrait;

    /**
     * @Route("/_get_dso_by_constellation/{constId}", name="get_dso_by_const_ajax")
     *
     * @param Request $request
     * @param string $constId
     * @param DsoDataTransformer $dsoDataTransformer
     * @param DsoManager $dsoManager
     *
     * @return JsonResponse
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __invoke(
        Request $request,
        string $constId,
        DsoDataTransformer $dsoDataTransformer,
        DsoManager $dsoManager
    ): JsonResponse
    {
        $offset = $request->query->get('offset') ?? 0;

        $listDso = $dsoDataTransformer->listVignettesView($dsoManager->getListDsoFromConst($constId, null,  $offset, AbstractRepository::SMALL_SIZE));
        $listDsoAll = $dsoManager->getListDsoFromConst($constId, null, 0, AbstractRepository::SMALL_SIZE);

        $result['dso'] = $listDso;
        $result['filters'] = $this->buildFiltersWithAll($listDsoAll);

        return new JsonResponse($result);
    }

}
