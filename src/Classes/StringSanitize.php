<?php
declare(strict_types=1);

namespace App\Classes;

use Transliterator;

/**
 *
 */
final class StringSanitize
{
    /** @var string */
    private string $string;
    /** @var \Transliterator */
    private \Transliterator $transliterator;

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
        $this->transliterator = Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);
    }

    /**
     * @return string
     */
    public function __invoke(): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z0-9]+)!', $this->transliterator->transliterate($this->string), $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode(trim(Utils::GLUE_DASH), $ret);
    }
}
