<?php

namespace App\Helpers;

use Symfony\Component\Templating\EngineInterface;

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
    private $locale;

    /**
     * MailHelper constructor.
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $templateEngine
     * @param string $locale
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templateEngine, $locale = 'en')
    {
        $this->mailer = $mailer;
        $this->templateEngine = $templateEngine;
        $this->locale = $locale;
    }

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $template
     * @param $content
     * @return int
     * @throws \Swift_TransportException
     */
    public function sendMail($from, $to, $subject, $template, $content): int
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
            throw new \Swift_TransportException('Error sending email');
        }
        return $sendMail;
    }

}