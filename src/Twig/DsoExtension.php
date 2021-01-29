<?php

namespace App\Twig;

use App\Classes\Utils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class DsoExtension
 * @package App\Twig
 */
class DsoExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    /**
     * DsoExtension constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('convert_ly_pc', [$this, 'convertLyToPc']),
            new TwigFilter('is_instance_of', [$this, 'isInstanceOf']),
            new TwigFilter('number_format_by_locale', [$this, 'numberFormatByLocale']),
            new TwigFilter('json_decode', [$this, 'jsonDecode'])
        ];
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uasort', [$this, 'uasort']),
            new TwigFunction('remove_element', [$this, 'removeElement']),
            new TwigFunction('build_api_filter', [$this, 'buildApiListFilters'])
        ];
    }


    /**
     * Convert distance in Light-Year into Parsec
     *
     * @param $dist
     *
     * @return string
     */
    public function convertLyToPc($dist): string
    {
        return Utils::numberFormatByLocale($dist*(Utils::PARSEC));
    }

    /**
     * @param $object
     * @param $class
     * @return bool
     */
    public function isInstanceOf($object, $class): bool
    {
        return is_a($object, $class, true);
    }

    /**
     * @param $number
     * @return string
     */
    public function numberFormatByLocale($number): string
    {
        return Utils::numberFormatByLocale($number);
    }

    /**
     * @param $str
     *
     * @return mixed
     * @throws \JsonException
     */
    public function jsonDecode($str)
    {
        return json_decode($str, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array $tab
     * @param $key
     *
     * @return mixed
     */
    public function uasort(array $tab, $key): array
    {
        uasort($tab, static function($a, $b) use($key) {
            return ($a[$key] < $b[$key]) ? -1 : 1;
        });
        return $tab;
    }

    /**
     * @param $arr
     * @param $value
     * @return mixed
     */
    public function removeElement($arr, $value): array
    {
        $index = array_search($value, $arr, true);
        unset($arr[$index]);
        return $arr;
    }


    /**
     * @param $filter
     *
     * @return array
     */
    public function buildApiListFilters($filter): array
    {
        switch($filter) {
            case 'catalog':
                $data = Utils::getOrderCatalog();
                break;
            case 'type':
                $data = Utils::getListTypeDso();
                break;
            case 'constellation':
                $data = [];
                break;
        }

        $html = '<table><thead><tr><td>Filter</td><td>Value</td></tr></thead><tbody>';

        foreach ($data as $item) {
            $html .= '<tr>';
            $html .= sprintf('<td>%s</td>', $item);
            $html .= sprintf('<td>%s</td>', $this->translator->trans(sprintf('%s.%s',$filter, $item)));
            $html .= '</tr>';
        }

        $html .= '<tbody>';
        $html .= '</tbody></table>';

        return ['message' => $html, 'type' => 'info'];
    }
}
