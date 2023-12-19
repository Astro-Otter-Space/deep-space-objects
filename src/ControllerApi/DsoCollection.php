<?php

namespace App\ControllerApi;

use App\Classes\Utils;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DsoCollection extends AbstractFOSRestController
{

    public function __construct(
        private DsoManager $dsoManager,
        private DsoRepository $dsoRepository
    ) { }

    /**
     * @Route("/dso/list", name="api_get_dso_collection", methods={"GET"})
     *
     * @QueryParam(name="constellation", requirements="\w+", default="")
     * @QueryParam(name="catalog", requirements="\w+", default="")
     * @QueryParam(name="type", requirements="\w+", default="")
     * @QueryParam(name="magnitude", requirements="\w+", default="")
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param DsoRepository $dsoRepository
     * @return View
     */
    public function getDsoListAction(
        Request $request,
        ParamFetcherInterface $paramFetcher,
        DsoRepository $dsoRepository
    ): View
    {
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');
        $filters = [];

        // Constellation
        $constellation = ("" !== $paramFetcher->get('constellation')) ? $paramFetcher->get('constellation') : null;
        if (!is_null($constellation)) {
            $filters['constellation'] = $constellation;
        }

        // Catalog
        $catalog = ("" !== $paramFetcher->get('catalog')) ? $paramFetcher->get('catalog') : null;
        if (!is_null($catalog)) {
            if (in_array($catalog, Utils::getOrderCatalog(), true)) {
                $filters['catalog'] = $catalog;
            } else {
                throw new InvalidParameterException("Parameter \"$catalog\" for catalog does not exist");
            }
        }

        // Type
        $type = ("" !== $paramFetcher->get('type')) ? $paramFetcher->get('type') : null;
        if ('all' === $type) {
            $type = null;
        }
        if (!is_null($type)) {
            if (in_array($type, Utils::getListTypeDso(), true)) {
                $filters['type'] = $type;
            } else {
                throw new InvalidParameterException("Parameter \"$type\" for type does not exist");
            }
        }

        // Magnitude
        $magnitude = ("" !== $paramFetcher->get('magnitude')) ? $paramFetcher->get('magnitude') : null;
        if (!is_null($magnitude)) {
            $filters['magnitude'] = $magnitude;
        }

        $authorizedFilters = $dsoRepository->getListAggregates(true);
        $filters = array_filter($filters, static function($key) use($authorizedFilters) {
            return in_array($key, $authorizedFilters, true);
        }, ARRAY_FILTER_USE_KEY);

        array_walk($filters, static function (&$value, $key) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        });

        [$listDsoId, $aggregates, $nbItems] = $this->dsoRepository
            ->setLocale($request->getLocale())
            ->getObjectsCatalogByFilters($offset, $filters, $limit, true);

        try {
            $listDso = $this->dsoManager->buildListDso($listDsoId);
        } catch (\JsonException|\ReflectionException $e) {
            $listDso = null;
        }

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $formatedData = $serializer->normalize([
            'data' => $listDso->getIterator()->getArrayCopy(),
            'filters' => $aggregates,
            'total' =>  $nbItems ?? 0
        ]);

        $view = View::create();
        $view->setData($formatedData);
        $view->setFormat('json');
        return $view;
    }
}
