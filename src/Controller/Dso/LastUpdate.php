<?php

declare(strict_types=1);

namespace App\Controller\Dso;

use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\BDD\UpdateData;
use App\Managers\DsoManager;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LastUpdate extends AbstractController
{

    use SymfonyServicesTrait, DsoTrait;

    /**
     * @Route({
     *   "en": "/last-update",
     *   "fr": "/mises-a-jour"
     * }, name="last_update_dso")
     *
     * @throws WsException
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function __invoke(
        Request $request,
        EntityManagerInterface $doctrineManager,
        DsoManager $dsoManager,
        DsoDataTransformer $dsoDataTransformer
    ): Response
    {
        $listDso = $dsoManager->getListDsoLastUpdated();

        /** @var UpdateData $lastUpdateData */
        $lastUpdateData = $doctrineManager->getRepository(UpdateData::class)->findOneBy([], ['date' => 'DESC']);

        $lastUpdateDate = $lastUpdateData->getDate()->format($this->translator->trans('dateFormatLong'));

        $title = $this->translator->trans('last_update_item', ['%date%' => $lastUpdateDate]);
        $titleBr = $this->translator->trans('last_update_title');
        $params = [
            'title' => $title,
            'breadcrumbs' => $this->buildBreadcrumbs(null, $this->router, $titleBr),
            'list_dso' => $dsoDataTransformer->listVignettesView($listDso)
        ];

        $response = $this->render('pages/last_dso_updated.html.twig', $params);
        $response->setSharedMaxAge(0)
            ->setLastModified(new \DateTime('now'));

        return $response;
    }
}
