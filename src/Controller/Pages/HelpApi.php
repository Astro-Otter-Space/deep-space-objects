<?php

namespace App\Controller\Pages;

use App\Entity\BDD\ApiUser;
use App\Forms\RegisterApiUsersFormType;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HelpApi extends AbstractController
{
    public const HTTP_TTL = 31556952;
    use SymfonyServicesTrait;

    /**
     * @Route({
     *     "fr": "/aide/api",
     *     "en": "/help/api",
     *     "es": "/help/api",
     *     "de": "/help/api",
     *     "pt": "/help/api"
     * }, name="help_api_page")
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param UserPasswordHasherInterface $passwordEncoder
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordEncoder
    ): Response
    {
        $isValid = false;
        $apiUser = new ApiUser();

        $optionsForm = [
            'method' => 'POST',
            'action' => $this->router->generate('help_api_page', ['_locale' => $request->getLocale()])
        ];

        $registerApiUserForm = $this->createForm(RegisterApiUsersFormType::class, $apiUser, $optionsForm);

        $registerApiUserForm->handleRequest($request);
        if ($registerApiUserForm->isSubmitted()) {
            if ($registerApiUserForm->isValid()) {
                $em = $doctrine->getManager();

                $apiUser->setPassword(
                    $passwordEncoder->hashPassword($apiUser, $registerApiUserForm->get('rawPassword')->getData())
                );

                $em->persist($apiUser);
                $em->flush();

                $isValid = true;
                $this->addFlash('form.success', 'form.api.success');
            } else {
                $this->addFlash('form.failed', 'form.error.message');
            }
        }

        $result['formRegister'] = $registerApiUserForm->createView();
        $result['is_valid'] = $isValid;

        $response = $this->render('pages/help_api.html.twig', $result);
        $response->setPublic();
        $response->setSharedMaxAge(self::HTTP_TTL);

        return $response;
    }

}
