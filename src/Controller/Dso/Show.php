<?php

declare(strict_types=1);

namespace App\Controller\Dso;

use App\Classes\Utils;
use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\DTO\DsoDTO;
use App\Managers\DsoManager;
use App\Service\AstrobinService;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Response\Image;
use Elastica\Exception\NotFoundException;
use JsonException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class Show extends AbstractController
{
    public const HTTP_TTL = 31556952;

    use DsoTrait, SymfonyServicesTrait;

    /**
     * @Route({
     *  "en": "/catalog/{id}",
     *  "fr": "/catalogue/{id}",
     *  "es": "/catalogo/{id}",
     *  "pt": "/catalogo/{id}",
     *  "de": "/katalog/{id}"
     * }, name="dso_show")
     *
     * @throws ReflectionException
     * @throws WsException
     * @throws JsonException
     */
    public function __invoke(
        Request $request,
        string $id,
        AstrobinService $astrobinService,
        DsoManager $dsoManager,
        DsoDataTransformer $dsoDataTransformer
    ): Response
    {
        $separator = trim(Utils::URL_CONCAT_GLUE);
        [$idDso, ] = explode($separator, $id);

        try {
            /** @var DsoDTO $dso */
            $params['dso'] = $dso = $dsoManager->getDso($idDso);

        } catch (NotFoundException $e) {
            throw new NotFoundHttpException();
        }

        $constellation = $dso->getConstellation();
        $params['desc'] = implode(Utils::GLUE_DASH, $dso->getDesigs());
        $params['dsoData'] = $dsoManager->formatVueData($dso);
        $params['constTitle'] = $constellation->title() ?? "";
        $params['last_update'] = $dso->getUpdatedAt();

        // Image cover
        $params['imgCoverAlt'] = ($dso->getAstrobin()->title) ? sprintf('"%s" by %s', $dso->getAstrobin()->title, $dso->getAstrobin()->user) : null;

        // List of Dso from same constellation
        $listDso = $dsoManager->getListDsoFromConst($dso->getConstellation()->getId(), $dso->getId(), 0, 20);

        $params['dso_by_const'] = $dsoDataTransformer->listVignettesView($listDso);
        $params['list_types_filters'] = $this->buildFiltersWithAll($listDso) ?? [];

        // Map
        $params['geojson_dso'] = [
            "type" => "FeatureCollection",
            "features" => [$dso->geoJson()]
        ];
        $params['geojson_center'] = $dso->getGeometry()['coordinates'];

        // List images
        $images = $astrobinService->listImagesBy($dso->getId());
        if (!is_null($images)) {
            if (1 < $images->count) {
                $listImages = array_map(static function(Image $image) {
                    return $image->url_regular;
                }, iterator_to_array($images));
            } elseif(1 === $images->count) {
                $listImages = [$images->getIterator()->current()->url_regular];
            }
        }

        $params['images'] = $listImages ?? []; //array_filter($listImages);


        $params['breadcrumbs'] = $this->buildBreadcrumbs($dso, $this->router, $dso->title());

        $response = $this->render('pages/dso.html.twig', $params);

        // cache expiration
        $response->setPublic();
        $response->setSharedMaxAge(self::HTTP_TTL);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        // cache validation
        $response->setLastModified($dso->getUpdatedAt());

        $listDsoIdHeaders = [
            md5(sprintf('%s_%s', $id, $request->getLocale())),
            md5(sprintf('%s_cover', $id))
        ];
        $response->headers->set('x-dso-id', implode(' ', $listDsoIdHeaders));

        return $response;
    }

}
