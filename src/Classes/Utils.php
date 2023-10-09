<?php

declare(strict_types=1);

namespace App\Classes;

use App\Command\ConvertCoordinatesCommand;
use DateTimeInterface;
use App\Classes\StringSanitization;

/**
 * @todo : create invokable classes or single services
 * Class Utils
 *
 * @package App\Classes
 */
final class Utils
{
    public const PARSEC = 0.3066020852;
    public const UNASSIGNED = 'unassigned';

    public const GLUE_DASH = ' - ';

    public const IMG_DEFAULT = '/build/images/default.png';
    public const IMG_LARGE_DEFAULT = '/build/images/default_large.jpg';

    public const FORMAT_DATE_ES = DateTimeInterface::RFC3339;

    public const CSV_DELIMITER = ';';
    public const CSV_ENCLOSURE = '"';

    public const DATA_GLUE = '.';
    public const DATA_CONCAT_GLUE = ' - ';
    public const URL_CONCAT_GLUE = '--';
    public const COMA_GLUE = ',';

    private static array $catalogMapping = [
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
        'Ba' => self::UNASSIGNED, 'Be' => self::UNASSIGNED, 'Bi' => self::UNASSIGNED, 'Bo' => self::UNASSIGNED, 'Bv' => self::UNASSIGNED,
        'B1' => self::UNASSIGNED, 'B2' => self::UNASSIGNED, 'B3' => self::UNASSIGNED, 'B4' => self::UNASSIGNED, 'B5' => self::UNASSIGNED, 'B6' => self::UNASSIGNED, 'B7' => self::UNASSIGNED, 'B8' => self::UNASSIGNED, 'B9' => self::UNASSIGNED,
        'K1' => self::UNASSIGNED, 'K2' => self::UNASSIGNED, 'K3' => self::UNASSIGNED, 'K4' => self::UNASSIGNED,
        'M1' => self::UNASSIGNED, 'M2' => self::UNASSIGNED, 'M3' => self::UNASSIGNED, 'M4' => self::UNASSIGNED, 'M7' => self::UNASSIGNED,
        'Mr' => self::UNASSIGNED,
        'Na' => self::UNASSIGNED,
        'Cz' => 'cz',
        'Ki' => 'kin',
        'Do' => self::UNASSIGNED,
        'Pa' => self::UNASSIGNED, 'Pe' => self::UNASSIGNED,
        'Ce' => self::UNASSIGNED,
        'Ru' => self::UNASSIGNED,
        'Ly' => 'lyn',
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
        'Vd' => 'vdb', 'VV' => self::UNASSIGNED, 'vy' => self::UNASSIGNED, 'VY' => self::UNASSIGNED
    ];

    private static array $orderCatalog = [
        // Main catalogs
        'messier',
        'ngc',
        'ic',
        'sh',
        'agc',
        'abl',
        'ldn',
        'lbn',
        // minor catalogs
        '3c',
        'arp',
        'am',
        'ant',
        'b',
        'bar',
        'bsl',
        'ber',
        'biu',
        'boc',
        'cr',
        'cld',
        'ced',
        'cz',
        'ddo',
        'doc',
        'dodz',
        'eso',
        'fle',
        'gum',
        'hb',
        'hf',
        'haf',
        'har',
        'hen',
        'hvd',
        'hic',
        'hcg',
        'hod',
        'hog',
        'hol',
        'huc',
        'k',
        'kin',
        'lat',
        'lod',
        'lon',
        'lyn',
        'mar',
        'may',
        'mel',
        'mgc',
        'mcg',
        'mkw',
        'mwp',
        'mzl',
        'mrk',
        'ocl',
        'pal',
        'pmb',
        'per',
        'pis',
        'rcw',
        'rsl',
        'ru',
        'sast',
        'sha',
        'shk',
        'sl',
        'stdr',
        'sto',
        'ter',
        'ton',
        'tr',
        'ugc',
        'vdb',
        'vv',
        'vy',
        self::UNASSIGNED
    ];

    private static array $listTypeDso = [
        'gg',
        'g',
        's',
        's0',
        'sd',
        'e',
        'i',
        'oc',
        'gc',
        'en',
        'bn',
        'bpn',
        'sfr',
        'rn',
        'pn',
        'snr',
        'dn',
        'pos',
        'sc',
        'vn',
        'ga',
        'q'
    ];

    /**
     * @var array
     */
    private static array $listTopics = [
        'contact' => 'contact.option.contact', // Simple contact
        'data' => 'contact.option.data', // Modifier/ajouter une donnée
        'astrobin' => 'contact.option.astrobin', // Demande d ajout image Astrobin
        'language' => 'contact.option.language', // Ajout d une langue
        //'account' => 'Problem with my account',
        'api' => 'contact.option.api',
        'other' => 'contact.option.other'
    ];

    /**
     * @var array
     */
    private static array $listPublics = [
        'all' => 'event.option.all',
        'deb' => 'event.option.deb',
        'conf' => 'event.option.conf'
    ];

    /**
     * @return array
     */
    public static function getCatalogMapping(): array
    {
        return self::$catalogMapping;
    }

    /**
     * List of all catalogs, ordering
     * @return array
     */
    public static function getOrderCatalog(): array
    {
        return self::$orderCatalog;
    }

    /**
     * List type of DSO
     * @return array
     */
    public static function getListTypeDso(): array
    {
        return self::$listTypeDso;
    }

    /**
     * @param $array
     * @return mixed
     */
    public static function utf8_converter($array): array
    {
        array_walk_recursive($array, static function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

    /**
     * @param $input
     * @return array|bool|string
     */
    public static function utf8_encode_deep(&$input): array|bool|string
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
     * @param int|float|null $number
     *
     * @return float|bool|int|string|null
     */
    public static function numberFormatByLocale(int|float|null $number): float|bool|int|string|null
    {
        if (!is_numeric($number)) {
            return $number;
        }
        $numberFormat = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        return $numberFormat->format($number);
    }


    /**
     * Transform a string name into URL name
     * @param $input
     *
     * @return string
     */
    public static function camelCaseUrlTransform($input): string
    {
//        /** @var \Transliterator $transliterator */
//        $transliterator = \Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', \Transliterator::FORWARD);
//        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z0-9]+)!', $transliterator->transliterate($input), $matches);
//
//        $ret = $matches[0];
//        foreach ($ret as &$match) {
//            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
//        }
//
//        return implode(trim(self::GLUE_DASH), $ret);
        $sanitization = new StringSanitization;
        return $sanitization($input);
    }

    /**
     * List of topics for contact form
     */
    public static function listTopicsContact(): array
    {
        return self::$listTopics;
    }

    /**
     * @return array
     */
    public static function listEventPublic(): array
    {
        return self::$listPublics;
    }
}
