<?php

namespace App\Controller\Pages;

use App\Classes\Utils;
use App\Forms\ContactFormType;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use App\Service\MailService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Entity\BDD\Contact as ContactEntity;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class Contact extends AbstractController
{
    use SymfonyServicesTrait;
    public const HTTP_TTL = 31556952;
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @Route({
     *     "fr": "/contactez-nous",
     *     "en": "/contact-us",
     *     "de": "/kontaktiere-uns",
     *     "es": "/contactenos",
     *     "pt": "/contate-nos"
     * }, name="contact")
     * @param Request $request
     * @param MailService $mailService
     * @param string $receiverMail
     *
     * @return Response
     */
    public function __invoke(Request $request, MailService $mailService, string $receiverMail): Response
    {
        $isValid = false;
        $optionsForm = [
            'method' => 'POST',
            'action' => $this->router->generate('contact'),
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ];

        $contact = new ContactEntity();
        $contactForm = $this->createForm(ContactFormType::class, $contact, $optionsForm);

        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted()) {
            if ($contactForm->isValid()) {
                /** @var ContactEntity $contactData */
                $contactData = $contactForm->getData();
                $contactData->setLabelCountry(Countries::getName($contactData->getCountry(), $request->getLocale()));

                $templates = [
                    'html' => 'includes/emails/contact.html.twig',
                    'text' => 'includes/emails/contact.txt.twig'
                ];

                $subject = '[Contact] - ' . $this->translator->trans(Utils::listTopicsContact()[$contactData->getTopic()]);
                $content['contact'] = $contactData;

                try {
                    $mailService->sendMail($contactData->getEmail(), $receiverMail, $subject, $templates, $content);
                    $sendMail = true;
                } catch(TransportExceptionInterface $e) {
                    $this->logger->error(sprintf('Error sending mail : %s', $e->getMessage()));
                    $sendMail = false;
                }

                if (true === $sendMail) {
                    $this->addFlash('form.success','form.ok.sending');
                    $isValid = true;
                } else {
                    $this->addFlash('form.failed','form.error.sending');
                }
            } else {
                $this->addFlash('form.failed','form.error.message');
            }
        }

        $result['formContact'] = $contactForm->createView();
        $result['is_valid'] = $isValid;

        $response = $this->render('pages/contact.html.twig', $result);
        $response->setSharedMaxAge(self::HTTP_TTL);
        $response->setPublic();

        return $response;
    }

}
