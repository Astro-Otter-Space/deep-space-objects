<?php


namespace App\ControllerApi;

use App\Entity\ApiUser;
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
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();

        $username = $request->request->get('_username');
        $password = $request->request->get('_password');
        $email = $request->request->get('_email');

        /** @var ApiUser $user */
        $user = new ApiUser($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);

        try {
            $em->persist($user);
            $em->flush();

            $response = ['success' => $user->getUsername(), 'code' => Response::HTTP_OK];
        } catch (DBALException $e) {
            $response = [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return new JsonResponse($response);
    }

}
