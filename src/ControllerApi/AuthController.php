<?php


namespace App\ControllerApi;

use App\Entity\BDD\ApiUser;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AuthController
 * @package App\ControllerApi
 */
class AuthController extends AbstractController
{

    /**
     * @Route("/auth/register", name="api_auth_register", methods={"POST"})
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();

        $email = $request->request->get('email');
        $rawPassword = $request->request->get('rawPassword');

        /** @var ApiUser $user */
        $user = new ApiUser();
        $user->setEmail($email);
        $user->setIsActive(true);
        $user->setPassword($encoder->encodePassword($user, $rawPassword));

        $em->persist($user);
        $em->flush();

        $response = ['success' => $user->getUsername(), 'code' => Response::HTTP_CREATED];

        return new JsonResponse($response);
    }

}
