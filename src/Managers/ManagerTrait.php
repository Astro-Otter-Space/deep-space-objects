<?php


namespace App\Managers;

use App\Entity\AbstractEntity;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait ManagerTrait
 *
 * @package App\Managers
 */
trait ManagerTrait
{
     private $translatorInterface;

    /**
     * ManagerTrait constructor.
     *
     * @param TranslatorInterface $translatorInterface
     */
    public function __construct(TranslatorInterface $translatorInterface)
    {
        $this->translatorInterface = $translatorInterface;
    }


    /**
     * Build a "table" of data (translated if needed) from Entity with translated label
     * @param $entity
     * @param $listFields
     *
     * @return array
     */
    public function formatEntityData($entity, $listFields)
    {
        /** @var TranslatorInterface $translate */
        $translate = $this->translatorInterface;

        $entityToArray = $entity->toArray();

        return array_map(function($value, $key) use($translate, $listFields) {

            if (!is_array($value)) {
                $valueTranslated = $translate->trans($value, ['%count%' => 1]);
            } else {
                $valueTranslated = implode(AbstractEntity::DATA_CONCAT_GLUE, array_map(function($item) use($translate) {
                    return $translate->trans($item, ['%count%' => 1]);
                }, $value));
            }

            return [
                'col0' => $translate->trans($key, ['%count%' => 1]),
                'col1' => (in_array($key, $listFields)) ? $valueTranslated: $value
            ];
        }, $entityToArray, array_keys($entityToArray));

    }

}