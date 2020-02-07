<?php

namespace App\Command;

use App\Service\SocialNetworks\WebServices\FacebookWs;
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

    /**
     * DsoPostSocialNetworkCommand constructor.
     *
     * @param FacebookWs $facebookWs
     */
    public function __construct(FacebookWs $facebookWs)
    {
        $this->facebookWs = $facebookWs;
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $postFb = $this->facebookWs->sendPost(null);
        dump($postFb);
        return 0;
    }
}
