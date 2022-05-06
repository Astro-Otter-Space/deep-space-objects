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
}
