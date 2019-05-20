<?php

namespace App\Helpers;

use App\Classes\Utils;
use App\Entity\Constellation;
use App\Entity\Dso;
use App\Entity\Observation;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use App\Repository\ObservationRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class UrlGenerateHelper
 * @package App\Helpers
 */
class UrlGenerateHelper
{

    /** @var RouterInterface  */
    private $router;

    /**
     * UrlGenerateHelper constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Build URL for entities
     *
     * @param $entity
     *
     * @return string
     */
    public function generateUrl($entity)
    {
        $url = '';
        if ($entity instanceof Dso || $entity instanceof Constellation || $entity instanceof Observation) {
            $id = strtolower($entity->getId());
            switch ($entity::getIndex()) {
                case DsoRepository::INDEX_NAME:
                    if (!empty($entity->getAlt())) {
                        $name = Utils::camelCaseUrlTransform($entity->getAlt());
                        $id = implode(trim($entity::URL_CONCAT_GLUE), [$id, $name]);
                    }

                    $url = $this->router->generate('dso_show', ['id' => $id]);
                    break;

                case ConstellationRepository::INDEX_NAME:
                    $url = $this->router->generate('constellation_show', ['id' => $id]);
                    break;

                case ObservationRepository::INDEX_NAME:
                    $name = Utils::camelCaseUrlTransform($entity->getName());
                    $url = $this->router->generate('observation_show', ['name' => $name]);
                    break;

                default:
                    $url = $this->router->generate('homepage');
            }
        }
        return $url;
    }

}
