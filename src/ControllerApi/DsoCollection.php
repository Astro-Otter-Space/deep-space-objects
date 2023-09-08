<?php

namespace App\ControllerApi;

use App\Classes\Utils;
use App\Managers\DsoManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DsoCollection extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/dso/list", name="api_get_dso_collection")
     *
     * @Rest\QueryParam(name="constellation", requirements="\w+", default="")
     * @Rest\QueryParam(name="catalog", requirements="\w+", default="")
     * @Rest\QueryParam(name="type", requirements="\w+",default="")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     *
     * @param ParamFetcher $paramFetcher
     * @param DsoManager $dsoManager
     * @return Response
     */
    public function __invoke(
        ParamFetcher $paramFetcher,
        DsoManager $dsoManager
    ): Response
    {
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $constellation = ("" !== $paramFetcher->get('constellation')) ? $paramFetcher->get('constellation') : null;
        if (!is_null($constellation)) {
            $filters['constellation'] = $constellation;
        }

        $catalog = ("" !== $paramFetcher->get('catalog')) ? $paramFetcher->get('catalog') : null;
        if (!is_null($catalog)) {
            if (in_array($catalog, Utils::getOrderCatalog(), true)) {
                $filters['catalog'] = $catalog;
            } else {
                throw new InvalidParameterException("Parameter \"$catalog\" for catalog does not exist");
            }
        }

        $type = ("" !== $paramFetcher->get('type')) ? $paramFetcher->get('type') : null;
        if (!is_null($type)) {
            if (in_array($type, Utils::getListTypeDso(), true)) {
                $filters['type'] = $type;
            } else {
                throw new InvalidParameterException("Parameter \"$type\" for type does not exist");
            }
        }

        array_walk($filters, static function (&$value, $key) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        });

        [$listDsoId, ,] = $this->dsoRepository
            ->setLocale('en')
            ->getObjectsCatalogByFilters($offset, $filters, $limit, true);

        try {
            $listDso = $dsoManager->buildListDso($listDsoId);
        } catch (\JsonException|\ReflectionException $e) {
            $listDso = null;
        }

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $formatedData = $serializer->normalize($listDso->getIterator()->getArrayCopy());

        $view = $this->view($formatedData, Response::HTTP_OK);
        $view->setFormat(DataController::JSON_FORMAT);

        return $this->handleView($view);
    }
}
