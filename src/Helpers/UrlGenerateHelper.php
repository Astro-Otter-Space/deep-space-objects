<?php


namespace App\Helpers;

use App\Entity\Constellation;
use App\Entity\Dso;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
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
     * @param $entity
     * @return string
     */
    public function generateUrl($entity)
    {
        $url = '';
        if ($entity instanceof Dso or $entity instanceof Constellation) {
            switch ($entity::getIndex()) {
                case DsoRepository::INDEX_NAME:
                    $url = $this->router->generate('dso_show', ['id' => $entity->getId()]);
                    break;

                case ConstellationRepository::INDEX_NAME:
                    $url = $this->router->generate('constellation_show', ['id' => $entity->getId()]);
                    break;

                default:
                    $url = $this->router->generate('homepage');
            }
        }
        return $url;
    }

}
