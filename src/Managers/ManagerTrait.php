<?php


namespace App\Managers;

use App\Entity\ES\AbstractEntity;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait ManagerTrait
 *
 * @package App\Managers
 */
trait ManagerTrait
{

    /**
     * Build a "table" of data (translated if needed) from Entity with translated label
     *
     * @param $entityArray
     * @param $listFields
     * @param TranslatorInterface $translatorInterface
     *
     * @return array
     */
    public function formatEntityData($entityArray, $listFields, TranslatorInterface $translatorInterface): array
    {
        return array_map(function($value, $key) use($translatorInterface, $listFields) {
            if (!is_array($value)) {
                $valueTranslated = $translatorInterface->trans($value, ['%count%' => 1]);
                $nbItems = 1;
            } else {
                $valueTranslated = implode(AbstractEntity::DATA_CONCAT_GLUE, array_map(static function($item) use($translatorInterface) {
                    return $translatorInterface->trans($item, ['%count%' => 1]);
                }, $value));
                $nbItems = count($value);
            }

            return [
                'col0' => $translatorInterface->trans($key, ['%count%' => $nbItems]),
                'col1' => (in_array($key, $listFields, true)) ? $valueTranslated: $value
            ];
        }, $entityArray, array_keys($entityArray));

    }

}
