<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\ControllerTraits\LayoutTrait;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DTOInterface;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LayoutController
 * @package App\Controller
 */
class LayoutController extends AbstractController
{
    use LayoutTrait;

    public const HTTP_TTL = 31556952;

    /** @var DsoRepository */
    private DsoRepository $dsoRepository;

    /**
     * @Route("/load/data/{file}", name="data_celestial")
     * @param Request $request
     * @param string $kernelProjectDir
     * @param string $file
     *
     * @return JsonResponse
     * @throws \JsonException
     */
    public function getStarsFromConst(Request $request, string $kernelProjectDir, string $file): JsonResponse
    {
        preg_match('/stars.([A-Za-z]{3}|([0-9]{1,2})).json/', $file, $matches);
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => []
        ];

        if ($matches) {
            $match = $matches[1];
            if (in_array($match, [6, 8, 14], true)) {
                $fileJson = file_get_contents($kernelProjectDir . '/public/build/data/' . sprintf('stars.%d.json', $match));

                $geojson = json_decode($fileJson, true, 512, JSON_THROW_ON_ERROR);
            } else {
                /** @var \Generator $readFile */
                /*$readFile = function($file) {
                    $h = fopen($file, 'r+');
                    while(!feof($h)) {
                        yield fgets($h);
                    }
                    fclose($h);
                };

                $fileJson = $readFile($kernel->getProjectDir() . '/public/build/data/stars.14.json');*/

                $fileJson = file_get_contents($kernelProjectDir . '/public/build/data/stars.8.json');
                $dataJson = json_decode($fileJson, true, 512, JSON_THROW_ON_ERROR);
                $filteredStars = array_filter($dataJson['features'], static function ($starData) use ($match) {
                    return $match === $starData['properties']['con'];
                });

                $geojson = [
                    'type' => 'FeatureCollection',
                    'features' => array_values($filteredStars)
                ];
            }
        } else {
            $filePath = sprintf('%s/public/build/data/%s', $kernelProjectDir, $file);
            if (file_exists($filePath)) {
                $fileJson = file_get_contents($filePath);
                $geojson = json_decode($fileJson, true, 512, JSON_THROW_ON_ERROR);
            }
        }

        $jsonResponse = new JsonResponse($geojson, Response::HTTP_OK);
        $jsonResponse->setSharedMaxAge(self::HTTP_TTL);
        $jsonResponse->setPublic();

        return $jsonResponse;
    }

}
