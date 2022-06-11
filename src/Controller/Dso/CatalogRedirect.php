<?php

declare(strict_types=1);

namespace App\Controller\Dso;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class CatalogRedirect extends AbstractController
{
    /**
     * @Route({
     *  "en": "/catalogs/{catalog}",
     *  "fr": "/catalogues/{catalog}",
     *  "es": "/catalogos/{catalog}",
     *  "pt": "/catalogos/{catalog}",
     *  "de": "/kataloge/{catalog}"
     * }, name="dso_catalog_redirect")
     * @param Request $request
     * @param string|null $catalog
     *
     * @return RedirectResponse
     */
    public function __invoke(Request $request, ?string $catalog): RedirectResponse
    {
        return $this->redirectToRoute('dso_catalog', ['catalog' => $catalog]);
    }
}
