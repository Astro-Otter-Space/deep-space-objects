<?php

namespace App\Controller\Pages;

use App\Classes\Utils;
use App\Entity\DTO\DTOInterface;
use App\Managers\DsoManager;
use App\Repository\AbstractRepository;
use App\Repository\DsoRepository;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *
 */
class Download extends AbstractController
{
    private DsoRepository $dsoRepository;
    private TranslatorInterface $translator;

    /**
     * PageController constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(DsoRepository $dsoRepository, TranslatorInterface $translator)
    {
        $this->dsoRepository = $dsoRepository;
        $this->translator = $translator;
    }


    /**
     * @Route({
     *     "fr": "/telechargement-donnees",
     *     "en": "/download-data",
     *     "de": "/download-data",
     *     "es": "/download-data",
     *     "pt": "/download-data",
     * }, name="download_data")
     * @param Request $request
     * @param DsoManager $dsoManager
     *
     * @return StreamedResponse
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __invoke(Request $request, DsoManager $dsoManager): StreamedResponse
    {
        $filters = [];
        $header = [
            'Name',
            $this->translator->trans('desigs'),
            'Description',
            $this->translator->trans('type'),
            'Constellation',
            $this->translator->trans('magnitude'),
            $this->translator->trans('dec'),
            $this->translator->trans('ra'),
            $this->translator->trans('distPc'),
            $this->translator->trans('distAl')
        ];

        // Retrieve list filters
        if (0 < $request->query->count()) {
            $authorizedFilters = $this->dsoRepository->getListAggregates(true);

            // Removed unauthorized keys
            $filters = array_filter($request->query->all(), static function($key) use($authorizedFilters) {
                return in_array($key, $authorizedFilters, true);
            }, ARRAY_FILTER_USE_KEY);

            // Sanitize data (todo : try better)
            array_walk($filters, static function (&$value, $key) {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
            });
        }

        [$listDsoId,,] = $this->dsoRepository->setLocale($request->getLocale())->getObjectsCatalogByFilters(0, $filters, AbstractRepository::MAX_SIZE, true);
        $listDso = $dsoManager->buildListDso($listDsoId);
        $data = array_map(function(DTOInterface $dso) {
            return [
                $dso->title(),
                implode(Utils::COMA_GLUE, array_filter($dso->getDesigs())),
                $dso->getDescription(),
                $this->translator->trans($dso->getType()),
                $dso->getConstellation()->title(),
                $dso->getMagnitude() ?? 999,
                $dso->getDeclinaison(),
                $dso->getRightAscencion(),
                $dso->distanceLightYears() ?? 0,
                $dso->distanceParsecs() ?? 0
            ];
        }, iterator_to_array($listDso));

        $data = array_merge([$header], $data);

        $now = new \DateTime();
        $fileName = sprintf('dso_data_%s_%s.csv', $request->getLocale(), $now->format('Ymd_His'));

        $response = new StreamedResponse(function() use ($data) {
            $handle = fopen('php://output', 'rb+');
            foreach ($data as $r) {
                fputcsv($handle, $r, Utils::CSV_DELIMITER, Utils::CSV_ENCLOSURE);
            }
            fclose($handle);
        });

        $response->headers->set('content-type', 'application/force-download');
        $response->headers->set('Content-Disposition', sprintf('attachement; filename="%s"', $fileName));

        return $response;
    }
}
