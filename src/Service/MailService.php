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
    private Environment $templateEngine;
    private string $userMail;
    private string $pwdMail;

    /**
     * MailService constructor.
     *
     * @param Environment $templateEngine
     */
    public function __construct(Environment $templateEngine)
    {
        $this->templateEngine = $templateEngine;
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
     * Create transport SMTP and send email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param array $template
     * @param array $content
     *
     * @throws TransportExceptionInterface
     */
    public function sendMail(string $from, string $to, string $subject, array $template, array $content): void
    {
        $transport = new GmailSmtpTransport($this->getUserMail(), $this->getPwdMail());

        /** @var MailerInterface $mailer */
        $mailer = new Mailer($transport);

        /** @var Email $email */
        $email = $this->buildEmail($from, $to, $subject, $template, $content);

        if (!is_null($email)) {
            $mailer->send($email);
        }
    }

    /**
     * Build an instance of Email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param array $template
     * @param array $content
     *
     * @return Email|null
     */
    private function buildEmail(string $from, string $to, string $subject, array $template, array $content):? Email
    {
        /** @var Email $email */
        try {
            $email =  (new Email())
                ->from($from)
                ->to($to)
                ->subject($subject);

            if (array_key_exists('html', $template)) {
                $email->html($this->templateEngine->render($template['html'], $content));
            }

            if (array_key_exists('text', $template)) {
                $email->text($this->templateEngine->render($template['text'], $content));
            }

            return $email;
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            return null;
        }
    }

}
