<?php


namespace App\Controller\ControllerTraits;

use App\Entity\Dso;
use App\Entity\ListDso;
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
    public function setTranslatorInterface(TranslatorInterface $translatorInterface)
    {
        $this->translatorInterface = $translatorInterface;
        return $this;
    }


    /**
     * @param ListDso|array $listDso
     *
     * @return array
     */
    protected function buildFilters(ListDso $listDso)
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
    public function buildFiltersWithAll($listDso)
    {
        $allFilters = [
            [
                'value' => 1,
                'label' => $this->translatorInterface->trans('hem.all')
            ]
        ];

        return array_merge($allFilters, $this->buildFilters($listDso));
    }

}