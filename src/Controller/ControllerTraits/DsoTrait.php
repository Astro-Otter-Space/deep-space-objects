<?php


namespace App\Controller\ControllerTraits;

use App\Entity\ES\AbstractEntity;
use App\Entity\ES\Constellation;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait DsoTrait
 *
 * @package App\Controller\ControllerTraits
 */
trait DsoTrait
{
    /** @var  */
    private $listFilters = [];

    /** @var TranslatorInterface */
    private $translatorInterface;

    /**
     * @param mixed $translatorInterface
     *
     * @return DsoTrait
     */
    public function setTranslatorInterface(TranslatorInterface $translatorInterface): self
    {
        $this->translatorInterface = $translatorInterface;
        return $this;
    }


    /**
     * @param ListDso|array $listDso
     *
     * @return array
     */
    protected function buildFilters(ListDso $listDso): array
    {
        if (0 < $listDso->getIterator()->count()) {
            $listDso = ($listDso instanceof ListDso) ? iterator_to_array($listDso) : $listDso;

            $this->listFilters = array_map(function(Dso $dsoData) {
                return [
                    'value' => $dsoData->getType(),
                    'label' => $this->translatorInterface->trans(sprintf('type.%s', $dsoData->getType()))
                ];
            }, $listDso);
        }

        return array_unique($this->listFilters, SORT_REGULAR);
    }

    /**
     * @param $listDso
     *
     * @return array
     */
    public function buildFiltersWithAll($listDso): array
    {
        $allFilters = [
            [
                'value' => 1,
                'label' => $this->translatorInterface->trans('hem.all')
            ]
        ];

        return array_merge($allFilters, $this->buildFilters($listDso));
    }

    /**
     * @param array|ListDso $data
     * @param int $codeHttp
     *
     * @return array
     */
    public function buildJsonApi($data, $codeHttp): array
    {
        $status = (in_array(substr($codeHttp, 0, 1), [4, 5])) ? 'error' : 'success';
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
     * @param AbstractEntity $entity
     * @param RouterInterface $router
     * @param string $title
     *
     * @return array
     */
    public function buildBreadcrumbs(AbstractEntity $entity, RouterInterface $router, string $title): array
    {
       $breadcrumbs = [];

        $breadcrumbs['level_1'] = [
            'label' => $this->translatorInterface->trans('menu.homepage'),
            'url' => $router->generate('homepage')
        ];

        $class = get_class($entity);

        switch ($class) {
            case Dso::class:
                $breadcrumbs['level_2'] = [
                    'label' => $this->translatorInterface->trans('catalogs'),
                    'url' => $router->generate('dso_catalog')
                ];
            break;

            case Constellation::class:
                $breadcrumbs['level_2'] = [
                    'label' => $this->translatorInterface->trans('constId', ['%count%' => 2]),
                    'url' => $router->generate('constellation_list')
                ];
            break;
        }

        $breadcrumbs['level_3'] = [
            'label' => $title,
            'url' => null
        ];

       return $breadcrumbs;

    }
}
