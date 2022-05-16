<?php

namespace App\Controller\ControllerTraits;

trait LayoutTrait
{


    /**
     * @param string|null $githubLink
     * @param string|null $facebookLink
     * @param string|null $twitterLink
     *
     * @return array
     */
    private function ctaFooter(?string $githubLink, ?string $facebookLink, ?string $twitterLink): array
    {
        $tab = [];
        if ($facebookLink) {
            $tab['facebook'] = [
                'label' => ucfirst('facebook'),
                'path' => $facebookLink,
                'blank' => true,
                'icon_class' => 'facebook'
            ];
        }

        if ($twitterLink) {
            $tab['twitter'] = [
                'label' => ucfirst('twitter'),
                'path' => $twitterLink,
                'blank' => true,
                'icon_class' => 'twitter'
            ];
        }

        if ($githubLink) {
            $tab['github'] = [
                'label' => ucfirst('github'),
                'path' => $githubLink,
                'blank' => true,
                'icon_class' => 'github'
            ];
        }
        return $tab;
    }

    /**
     * Build left side menu
     *
     * @param string $locale
     * @param array $listKeysMenu
     * @return array
     */
    private function buildMenu(string $locale, array $listKeysMenu): array
    {
        $menu = [
            'lastUpdate' => [
                'label' => $this->translator->trans('last_update_title'),
                'path' => $this->router->generate(sprintf('last_update_dso.%s', $locale)),
                'icon_class' => 'bell'
            ],
            'catalog' => [
                'label' => $this->translator->trans('catalogs'),
                'path' => $this->router->generate(sprintf('dso_catalog.%s', $locale)),
                'icon_class' => 'galaxy-cluster',
                'subMenu' => $this->buildSubMenu($locale, ['messier', 'ngc', 'ic', 'sh'])
            ],
            'constellation' => [
                'label' => $this->translator->trans('constId', ['%count%' => 2]),
                'path' => $this->router->generate(sprintf('constellation_list.%s', $locale)),
                'icon_class' => 'constellation'
            ],
            'map' => [
                'label' => $this->translator->trans('skymap'),
                'path' => $this->router->generate(sprintf('skymap.%s', $locale)),
                'icon_class' => 'planet'
            ],
            'contact' => [
                'label' => $this->translator->trans('contact.title'),
                'path' => $this->router->generate(sprintf('contact.%s', $locale)),
                'icon_class' => 'contact'
            ]
        ];

        return array_filter($menu, static function ($key) use ($listKeysMenu) {
            return in_array($key, $listKeysMenu, true);
        }, ARRAY_FILTER_USE_KEY);

    }

    /**
     * @param string $locale
     * @param array $listCatalogs
     *
     * @return array
     */
    public function buildSubMenu(string $locale, array $listCatalogs): array
    {
        return array_map(function(string $catalog) use($locale) {
            return [
                'label' => $this->translator->trans(sprintf('catalog.%s', $catalog)),
                'path' => $this->router->generate(sprintf('dso_catalog_redirect.%s', $locale), ['catalog' => $catalog])
            ];

        }, $listCatalogs);
    }
}
