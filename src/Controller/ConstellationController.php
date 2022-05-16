<?php

namespace App\Controller;

use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\DsoDataTransformer;
use App\Managers\DsoManager;
use App\Repository\AbstractRepository;
use App\Repository\DsoRepository;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConstellationController
 * @package App\Controller
 */
class ConstellationController extends AbstractController
{
    use DsoTrait, SymfonyServicesTrait;

    private DsoManager $dsoManager;

    /**
     * ConstellationController constructor.
     *
     * @param DsoManager $dsoManager
     */
    public function __construct(DsoManager $dsoManager)
    {
        $this->dsoManager = $dsoManager;
    }

    /**
     * @Route("/_get_dso_by_constellation/{constId}", name="get_dso_by_const_ajax")
     * @param Request $request
     * @param string $constId
     * @param DsoDataTransformer $dsoDataTransformer
     *
     * @return JsonResponse
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function dsoByConstellationAjax(Request $request, string $constId, DsoDataTransformer $dsoDataTransformer): JsonResponse
    {
        $offset = $request->query->get('offset');

        $listDso = $dsoDataTransformer->listVignettesView($this->dsoManager->getListDsoFromConst($constId, null,  $offset, AbstractRepository::SMALL_SIZE));
        $listDsoAll = $this->dsoManager->getListDsoFromConst($constId, null, 0, AbstractRepository::SMALL_SIZE);

        $result['dso'] = $listDso;
        $result['filters'] = $this->buildFiltersWithAll($listDsoAll);

        return new JsonResponse($result);
    }

}
