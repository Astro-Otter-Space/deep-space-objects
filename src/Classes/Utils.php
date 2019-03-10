<?php

namespace App\Classes;

/**
 * Class Utils
 *
 * @package App\Classes
 */
class Utils
{
    const PARSEC = 0.3066020852;
    const UNASSIGNED = 'unassigned';

    const GLUE_DASH = ' - ';

    const IMG_DEFAULT = '/build/images/default.jpg';

    private static $catalogMapping = [
        'NG' => 'ngc',
        'IC' => 'ic',
        'LD' => 'ldn',
        'Sh' => 'sh',
        'Cr' => 'cr',
        'St' => 'sto',
        'Ab' => 'abl',
        'UG' => 'ugc',
        'An' => self::UNASSIGNED, 'Ap' => self::UNASSIGNED, 'AP' => self::UNASSIGNED,
        'He' => self::UNASSIGNED,
        'Ba' => self::UNASSIGNED, 'Be' => self::UNASSIGNED, 'Bi' => self::UNASSIGNED, 'Bo' => self::UNASSIGNED,
        'B1' => self::UNASSIGNED, 'B2' => self::UNASSIGNED, 'B3' => self::UNASSIGNED, 'B4' => self::UNASSIGNED, 'B5' => self::UNASSIGNED, 'B6' => self::UNASSIGNED, 'B7' => self::UNASSIGNED, 'B8' => self::UNASSIGNED, 'B9' => self::UNASSIGNED,
        'K1' => self::UNASSIGNED, 'K2' => self::UNASSIGNED, 'K3' => self::UNASSIGNED, 'K4' => self::UNASSIGNED,
        'M1' => self::UNASSIGNED, 'M2' => self::UNASSIGNED, 'M3' => self::UNASSIGNED, 'M4' => self::UNASSIGNED, 'M7' => self::UNASSIGNED,
        'Cz' => self::UNASSIGNED,
        'Ki' => self::UNASSIGNED,
        'Do' => self::UNASSIGNED,
        'Pa' => self::UNASSIGNED, 'Pe' => self::UNASSIGNED,
        'Ce' => self::UNASSIGNED,
        'Ru' => self::UNASSIGNED,
        'Ly' => self::UNASSIGNED,
        'Ha' => self::UNASSIGNED, 'Ho' => self::UNASSIGNED, 'Hu' => self::UNASSIGNED,
        'H1' => self::UNASSIGNED, 'H2' => self::UNASSIGNED,
        'vd' => self::UNASSIGNED,
        'Ca' => self::UNASSIGNED,
        'La' => self::UNASSIGNED,
        'Me' => self::UNASSIGNED,
        '3C' => self::UNASSIGNED,
        'Te' => self::UNASSIGNED, 'To' => self::UNASSIGNED, 'Tr' => self::UNASSIGNED,
        'Gu' => self::UNASSIGNED, 'Gr' => self::UNASSIGNED,
        'Pi' => self::UNASSIGNED,
        'Fe' => self::UNASSIGNED,
        'Ro' => self::UNASSIGNED,
        'Jo' => self::UNASSIGNED,
        'J3' => self::UNASSIGNED, 'J9' => self::UNASSIGNED,
        'Vd' => self::UNASSIGNED, 'VV' => self::UNASSIGNED, 'vy' => self::UNASSIGNED, 'VY' => self::UNASSIGNED
    ];

    /**
     * @var array
     */
    private static $listTopics = [
        'contact' => 'contact.option.contact', // Simple contact
        'data' => 'contact.option.data', // Modifier/ajouter une donnée
        'astrobin' => 'contact.option.astrobin', // Demande d ajout image Astrobin
        //'account' => 'Problem with my account',
        'other' => 'contact.option.other'
    ];

    /**
     * @return array
     */
    public static function getCatalogMapping()
    {
        return self::$catalogMapping;
    }

    /**
     * @param $array
     * @return mixed
     */
    public static function utf8_converter($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

    /**
     * @param $input
     * @return bool|string
     */
    public static function utf8_encode_deep(&$input)
    {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                self::utf8_encode_deep($value);
            }

            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));

            foreach ($vars as $var) {
                self::utf8_encode_deep($input->$var);
            }
        }
        return $input;
    }


    /**
     * Format number from locale
     *
     * @param $number
     *
     * @return bool|string
     */
    public static function numberFormatByLocale($number)
    {
        /** @var \NumberFormatter $numberFormat */
        $numberFormat = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        return $numberFormat->format($number);
    }

    /**
     *
     */
    public static function listTopicsContact()
    {
        return self::$listTopics;
    }
}
