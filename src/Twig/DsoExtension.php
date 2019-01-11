<?php


namespace App\Twig;

use App\Classes\Utils;

/**
 * Class DsoExtension
 * @package App\Twig
 */
class DsoExtension extends \Twig_Extension
{
    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('convert_ly_pc', [$this, 'convertLyToPc']),
            new \Twig_SimpleFilter('is_instance_of', [$this, 'isInstanceOf']),
            new \Twig_SimpleFilter('number_format_by_locale', [$this, 'numberFormatByLocale'])
        ];
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('uasort', [$this, 'uasort']),
            new \Twig_SimpleFunction('remove_element', [$this, 'removeElement'])
        ];
    }


    /**
     * Convert distance in Light-Year into Parsec
     * @param $dist
     * @return float|int
     */
    public function convertLyToPc($dist)
    {
        return Utils::numberFormatByLocale($dist*(Utils::PARSEC));
    }

    /**
     * @param $object
     * @param $class
     * @return bool
     */
    public function isInstanceOf($object, $class)
    {
        return is_a($object, $class, true) ? true: false;
    }

    /**
     * @param $number
     * @return string
     */
    public function numberFormatByLocale($number)
    {
        return Utils::numberFormatByLocale($number);
    }

    /**
     * @param $tab
     * @param $key
     * @return
     */
    public function uasort($tab, $key)
    {
        uasort($tab, function($a, $b) use($key) {
            return ($a[$key] < $b[$key]) ? -1 : 1;
        });
        return $tab;
    }
    /**
     * @param $arr
     * @param $value
     * @return mixed
     */
    public function removeElement($arr, $value)
    {
        $index = array_search($value, $arr);
        unset($arr[$index]);
        return $arr;
    }
}
