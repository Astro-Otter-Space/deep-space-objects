<?php

namespace App\Command;

use App\Entity\BDD\ItemShared;
use App\Entity\ES\Dso;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use App\Service\SocialNetworks\WebServices\FacebookWs;
use App\Service\SocialNetworks\WebServices\TwitterWs;
use Astrobin\Exceptions\WsException;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\Exceptions\FacebookSDKException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Router;

/**
 * Class DsoPostSocialNetworkCommand
 *
 * @package App\Command
 *
 * https://stackoverflow.com/questions/32328734/facebook-sdk-v5-post-as-page-on-wall?answertab=active#tab-top
 */
class DsoPostSocialNetworkCommand extends Command
{
    protected static $defaultName = 'dso:post-social-network';

    /** @var FacebookWs */
    private $facebookWs;

    /** @var TwitterWs */
    private $twitterWs;

    /** @var EntityManagerInterface */
    private $em;

    /** @var DsoManager */
    private $dsoManager;

    /** @var DsoRepository */
    private $dsoRepository;

    /**
     * DsoPostSocialNetworkCommand constructor.
     *
     * @param FacebookWs $facebookWs
     * @param TwitterWs $twitterWs
     * @param EntityManagerInterface $em
     * @param DsoManager $dsoManager
     * @param DsoRepository $dsoRepository
     */
    public function __construct(FacebookWs $facebookWs, TwitterWs $twitterWs, EntityManagerInterface $em, /*DsoManager $dsoManager,*/ DsoRepository $dsoRepository)
    {
        $this->facebookWs = $facebookWs;
        $this->twitterWs = $twitterWs;
        $this->em = $em;
        //$this->dsoManager = $dsoManager;
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
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Dso $dso */
        $dso = $this->getDsoToPost();

        die();
        // Send to social networks
        $this->sendToTwitter($dso);
        $this->sendToFacebook($dso);


        // Record into BDD
        $this->addSharedItem($dso);

        return 0;
    }

    /**
     *
     */
    private function getDsoToPost(): Dso
    {
        // 1 Get all inserted
        $listSharedItems = $this->em->getRepository(ItemShared::class)->findAll();
        dump($listSharedItems);

        // 2 Get random item, filtered by above
        $listResult = $this->dsoRepository->getAstrobinId($listSharedItems);

        $dsoRandom = array_rand($listResult);

        $dsoId = key($dsoRandom);
        try {
            return $this->dsoManager->buildDso($dsoId);
        } catch (WsException $e) {
        } catch (\ReflectionException $e) {
        }
    }


    /**
     * @param $dso
     */
    private function sendToFacebook($dso)
    {
            return;
    }

    /**
     * @param Dso $dso
     */
    private function sendToTwitter(Dso $dso)
    {
        $title = $this->dsoManager->buildTitle($dso);
        $link = $this->dsoManager->getDsoUrl($dso, Router::ABSOLUTE_URL);
        $tweet = $this->twitterWs->postLink($title, $link, null);
    }

    /**
     * @param Dso $dso
     *
     * @throws \Exception
     */
    private function addSharedItem(Dso $dso): void
    {
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
    }
}
