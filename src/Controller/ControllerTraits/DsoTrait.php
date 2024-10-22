<?php

declare(strict_types=1);

namespace App\Controller\ControllerTraits;

use App\Entity\DTO\DsoDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Constellation;
use App\Entity\ES\Event;
use App\Entity\ES\ListDso;
use App\Entity\ES\Observation;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait DsoTrait
 *
 * @package App\Controller\ControllerTraits
 */
trait DsoTrait
{

    private array $listFilters = [];

    /**
     * @param ListDso|array $listDso
     *
     * @return array
     */
    protected function buildFilters(array|ListDso $listDso): array
    {
        if (0 < $listDso->getIterator()->count()) {
            $listDso = iterator_to_array($listDso);

            $this->listFilters = array_map(function(DTOInterface $dsoData) {
                return [
                    'value' => $dsoData->getType(),
                    'label' => $this->translator->trans(sprintf('type.%s', $dsoData->getType()))
                ];
            }, $listDso);
        }

        return array_unique($this->listFilters, SORT_REGULAR);
    }

    /**
     * @param ListDso $listDso
     *
     * @return array
     */
    public function buildFiltersWithAll(ListDso $listDso): array
    {
        $allFilters = [
            [
                'value' => 1,
                'label' => $this->translator->trans('hem.all')
            ]
        ];

        return array_merge($allFilters, $this->buildFilters($listDso));
    }

    /**
     * @param DTOInterface|ListDso $data
     * @param int $codeHttp
     *
     * @return array
     */
    public function buildJsonApi($data, int $codeHttp): array
    {
        $firstNumber = (int)($codeHttp / 100);
        $status = (in_array($firstNumber, [4, 5], true)) ? 'error' : 'success';
        $dataResponse = [
            'status' => $status,
            'code' => $codeHttp,
            'data' => $data
        ];

        if (is_array($data) && 1 < count($data)) {
            $dataResponse['count'] = count($data);
        }

        return $dataResponse;
    }


    /**
     * @param DTOInterface|null $entity
     * @param RouterInterface $router
     * @param string|null $title
     *
     * @return array
     */
    public function buildBreadcrumbs(?DTOInterface $entity, RouterInterface $router, ?string $title): array
    {
       $breadcrumbs = [];

        $breadcrumbs['level_1'] = [
            'label' => $this->translator->trans('menu.homepage'),
            'url' => $router->generate('homepage')
        ];

        if (!is_null($entity)) {
            $class = get_class($entity);

            switch ($class) {
                case DsoDTO::class:
                    $breadcrumbs['level_2'] = [
                        'label' => $this->translator->trans('catalogs'),
                        'url' => $router->generate('dso_catalog')
                    ];
                    break;

                case Constellation::class:
                    $breadcrumbs['level_2'] = [
                        'label' => $this->translator->trans('constId', ['%count%' => 2]),
                        'url' => $router->generate('constellation_list')
                    ];
                    break;
            }
        }

        if (!is_null($title)) {
            $breadcrumbs['level_3'] = [
                'label' => $title,
                'url' => null
            ];
        }

        return $breadcrumbs;
    }
}
