<?php

namespace App\Controller\Constellation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Download map constellation
 */
class DownloadMap extends AbstractController
{

    /**
     * @Route("/download/map/{id}",name="download_map")
     *
     * @param string $id
     * @param string $kernelProjectDir
     *
     * @return BinaryFileResponse
     */
    public function __invoke(
        string $id,
        string $kernelProjectDir
    ): BinaryFileResponse
    {
        $webPath = sprintf('%s/public/', $kernelProjectDir);

        $file = $webPath . sprintf('build/images/const_maps/%s.gif', strtoupper($id));

        if (!file_exists($file)) {
            throw new NotFoundHttpException(sprintf('File "%s" not exist anymore', $file));
        }

        $typeMimeGuesser = new FileinfoMimeTypeGuesser();

        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', $typeMimeGuesser->guessMimeType($file));

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($file)
        );

        return $response;
    }

}
