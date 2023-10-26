<?php

namespace App\ControllerApi;

use App\Classes\Utils;
use App\Entity\BDD\Contact as ContactEntity;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use App\Service\MailService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Contact extends AbstractFOSRestController
{
    use SymfonyServicesTrait;

    public function __construct(
        private MailService $mailService,
        private ValidatorInterface $validator,
        private string $receiverMail
    )
    { }

    /**
     * @Route("/contact", name="api_post_contact", methods={"POST"})
     * @ParamConverter("contact", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param ContactEntity $contact
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return View
     */
    public function __invoke(
        Request $request,
        ContactEntity $contact,
        ConstraintViolationListInterface $validationErrors
    ): View
    {
        $contact->setLabelCountry(Countries::getName($contact->getCountry(), $request->getLocale()));
        dump($contact, $validationErrors);
        $templates = [
            'html' => 'includes/emails/contact.html.twig',
            'text' => 'includes/emails/contact.txt.twig'
        ];
        $subject = '[Contact] - ' . $this->translator->trans(Utils::listTopicsContact()[$contact->getTopic()]);
        $content['contact'] = $contact;
        $view = View::create();

        try {
            $this->mailService->sendMail($contact->getEmail(), $this->receiverMail, $subject, $templates, $content);
            $view->setStatusCode(201)->setFormat('json');
        } catch(TransportExceptionInterface $e) {
            $view->setStatusCode(500);
        }


        $view->setStatusCode(201)->setFormat('json');
        return $view;
    }
}
