<?php

namespace App\Service;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailService
 *
 * @package App\Service
 */
class MailService
{
    /** @var Environment */
    private $templateEngine;

    /** @var string */
    private $senderMail;

    /** @var string */
    private $userMail;

    /** @var string */
    private $pwdMail;

    /** @var string */
    private $fromMail;

    /**
     * MailService constructor.
     *
     * @param Environment $templateEngine
     * @param string $senderMail
     */
    public function __construct(Environment $templateEngine, string $senderMail)
    {
        $this->templateEngine = $templateEngine;
        $this->senderMail = $senderMail;
    }

    /**
     * @return mixed
     */
    private function getUserMail(): string
    {
        return $this->userMail;
    }

    /**
     * @param mixed $userMail
     *
     * @return MailService
     */
    public function setUserMail($userMail): self
    {
        $this->userMail = $userMail;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getPwdMail(): string
    {
        return $this->pwdMail;
    }

    /**
     * @param mixed $pwdMail
     *
     * @return MailService
     */
    public function setPwdMail($pwdMail): self
    {
        $this->pwdMail = $pwdMail;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getFromMail(): string
    {
        return $this->fromMail;
    }

    /**
     * @param mixed $fromMail
     *
     * @return MailService
     */
    public function setFromMail($fromMail): self
    {
        $this->fromMail = $fromMail;
        return $this;
    }


    /**
     * @param string $to
     * @param string $subject
     * @param array $template
     * @param array $content
     *
     * @throws TransportExceptionInterface
     */
    public function sendMail(string $to, string $subject, array $template, array $content): void
    {
        /** @var GmailSmtpTransport $transport */
        $transport = new GmailSmtpTransport($this->getUserMail(), $this->getPwdMail());

        /** @var MailerInterface $mailer */
        $mailer = new Mailer($transport);

        /** @var Email $email */
        $email = $this->buildEmail($to, $subject, $template, $content);

        $mailer->send($email);
    }

    /**
     * @param string $to
     * @param string $subject
     * @param array $template
     * @param array $content
     *
     * @return Email|null
     */
    private function buildEmail(string $to, string $subject, array $template, array $content):? Email
    {
        /** @var Email $email */
        try {
            return (new Email())
                ->from($this->senderMail)
                ->to($to)
                ->subject($subject)
                ->html($this->templateEngine->render($template['html'], $content))
                ->text($this->templateEngine->render($template['text'], $content));
        } catch (LoaderError $e) {
            return null;
        } catch (RuntimeError $e) {
            return null;
        } catch (SyntaxError $e) {
            return null;
        }
    }

}
