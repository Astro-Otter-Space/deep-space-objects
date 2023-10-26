<?php

namespace App\ControllerApi;

use App\Entity\BDD\Contact as ContactEntity;
use App\Service\MailService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Contact extends AbstractFOSRestController
{

    public function __construct(
        private MailService $mailService,
        private string $receiverMail
    )
    { }

    /**
     * @Route("/contact", name="api_post_contact", methods={"POST"})
     * @ParamConverter("contact", converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param ContactEntity $contact
     * @return View
     */
    public function __invoke(
        Request $request,
        ContactEntity $contact
    ): View
    {
        dump($contact, $this->receiverMail);

        $view = View::create();
        $view->setStatus(201)->setFormat('json');
        return $view;
    }
}
