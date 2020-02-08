<?php

namespace App\Command;

use App\Entity\BDD\ItemShared;
use App\Repository\DsoRepository;
use App\Service\SocialNetworks\WebServices\FacebookWs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\Exceptions\FacebookSDKException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DsoPostSocialNetworkCommand
 *
 * @package App\Command
 */
class DsoPostSocialNetworkCommand extends Command
{
    protected static $defaultName = 'dso:post-social-network';

    /** @var FacebookWs */
    private $facebookWs;

    /** @var EntityManagerInterface */
    private $em;

    /** @var DsoRepository */
    private $dsoRepository;

    /**
     * DsoPostSocialNetworkCommand constructor.
     *
     * @param FacebookWs $facebookWs
     * @param EntityManagerInterface $em
     * @param DsoRepository $dsoRepository
     */
    public function __construct(FacebookWs $facebookWs, EntityManagerInterface $em, DsoRepository $dsoRepository)
    {
        $this->facebookWs = $facebookWs;
        $this->em = $em;
        $this->dsoRepository = $dsoRepository;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription('Post data on social networks')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws FacebookSDKException
     * @throws \Doctrine\ORM\ORMException
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 1 Get all inserted
        $listItems = $this->em->getRepository(ItemShared::class)->findAll();
        dump($listItems);
        die();
        // 2 Get random item, filtered by above
        $dsoId = array_rand($this->dsoRepository->getAstrobinId());

        $dso = $this->dsoRepository->getObjectById($dsoId, true);

        // 3 Post into social networks
        $postFb = $this->facebookWs->sendPost(null);
        dump($postFb);

        // 4 Save into BDD
        /** @var \DateTimeInterface $dateCreate */
        $dateCreate = new \DateTime();

        /** @var ItemShared $newSharedItem */
        $newSharedItem = new ItemShared();
        $newSharedItem->setIdDso($dso->getId())
            ->setAstrobinId($dso->getAstrobinId())
            ->setCreatedDate($dateCreate);

        $this->em->persist($newSharedItem);
        $this->em->flush();

        return 0;
    }
}
