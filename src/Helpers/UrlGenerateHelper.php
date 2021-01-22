<?php

namespace App\Helpers;

use App\Classes\Utils;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DsoDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Constellation;
use App\Entity\ES\Dso;
use App\Entity\ES\Event;
use App\Entity\ES\Observation;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use App\Repository\EventRepository;
use App\Repository\ObservationRepository;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

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
     * @param DTOInterface $entity
     * @param int $typeUrl
     * @param string $locale
     *
     * @return string
     */
    public function generateUrl(DTOInterface $entity, $typeUrl = Router::ABSOLUTE_PATH, string $locale = null): string
    {
        $url = '';
        if ($entity instanceof DsoDTO
            || $entity instanceof ConstellationDTO
//            || $entity instanceof Observation
//            || $entity instanceof Event
        ) {
            $id = strtolower($entity->getId());

            switch (get_class($entity)) {
                case DsoDTO::class:
                    if (!empty($entity->title())) {
                        $name = Utils::camelCaseUrlTransform($entity->getAlt());
                        $id = implode(trim(Utils::URL_CONCAT_GLUE), [$id, $name]);
                    }

                    $route = "dso_show";
                    $params = ['id' => $id];

                    if (!is_null($locale)) {
                        $route = sprintf('%s.%s', $route, $locale);
                        $params = ['id' => $id, '_locale' => $locale];
                    }
                    $url = $this->router->generate($route, $params, $typeUrl);
                    break;

                case ConstellationDTO::class:
                    $route = "constellation_show";

                    $name = Utils::camelCaseUrlTransform($entity->title());
                    $params = ['id' => $id, 'name' => $name];
                    if (!is_null($locale)) {
                        $params = ['id' => $id, 'name' => $name, '_locale' => $locale];
                    }

                    if (!is_null($locale)) {
                        $route = sprintf('%s.%s', $route, $locale);
                        $params = ['id' => $id, 'name' => $name, '_locale' => $locale];
                    }

                    $url = $this->router->generate($route, $params, $typeUrl);
                    break;

               /* case ObservationRepository::INDEX_NAME:
                    $name = Utils::camelCaseUrlTransform($entity->fieldsUrl());
                    $url = $this->router->generate('observation_show', ['name' => $name], $typeUrl);
                    break;

                case EventRepository::INDEX_NAME:
                    $name = Utils::camelCaseUrlTransform($entity->fieldsUrl());
                    $url = $this->router->generate('event_show', ['name' => $name], $typeUrl);
                    break;*/

                default:
                    $url = $this->router->generate('homepage');
            }
        }
        return $url;
    }
}
