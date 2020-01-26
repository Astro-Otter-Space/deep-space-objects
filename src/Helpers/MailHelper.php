<?php

namespace App\Helpers;

use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailHelper
 * @package App\Helpers
 */
class MailHelper
{
    const MIME_HTML = 'text/html';
    const MIME_TEXT = 'text/plain';

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var EngineInterface */
    private $templateEngine;

    /** @var string  */
    private $defaultLocale;


    /**
     * MailHelper constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param Environment $templateEngine
     * @param string $defaultLocale
     */
    public function __construct(\Swift_Mailer $mailer, Environment $templateEngine, string $defaultLocale)
    {
        $this->mailer = $mailer;
        $this->templateEngine = $templateEngine;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $template
     * @param $content
     *
     * @return int
     * @throws \Swift_TransportException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendMail(string $from, string $to, string $subject, array $template, array $content): int
    {
        /** @var \Swift_Message $message */
        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to);

        // HTML template
        if (array_key_exists('html', $template)) {
            $message->setBody(
                $this->templateEngine->render($template['html'], $content),
                self::MIME_HTML
            );
        }

        // Text template
        if (array_key_exists('text', $template)) {
            $message->addPart(
                $this->templateEngine->render($template['text'], $content),
                self::MIME_TEXT
            );
        }

        /** @var  $sendMail */
        $sendMail = $this->mailer->send($message);
        if (!$sendMail) {
            throw new \Swift_TransportException();
        }
        return $sendMail;
    }

}
