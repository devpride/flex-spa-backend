<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Component\Response\JsonSuccessResponse;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Service\Api\Component\Response\ErrorResponse;
use App\Service\Api\Component\Response\SuccessResponse;
use App\Service\Registration\PasswordlessRegistrationDto;
use App\Service\Registration\PasswordlessRegistrationMailer;
use App\Service\Security\PasswordGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="v1/users")
 */
class UserController extends Controller
{
    /**
     * @Route(path="/", name="user_create")
     * @Method("POST")
     * @param Request           $request
     * @param UserFactory       $factory
     * @param PasswordGenerator $passwordGenerator
     *
     * @return Response
     */
    public function create(
        Request $request,
        UserFactory $factory,
        PasswordGenerator $passwordGenerator,
        PasswordlessRegistrationMailer $mailer
    ) : Response {
        if (is_null($request->get('email')) || is_null($request->get('username'))) {
            return new ErrorResponse('Missing required fields: "email", "username".');
        }

        $generatedPassword = $passwordGenerator->generate();

        $user = $factory->create(
            $request->get('email'),
            $request->get('username'),
            $request->get('fullName', 'Jon Snow'),
            $generatedPassword
        );

        $mailer->mailAsync(new PasswordlessRegistrationDto($user->getEmail(), $generatedPassword));

        return new SuccessResponse($this->mapUser($user), JsonSuccessResponse::HTTP_CREATED);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    private function mapUser(User $user) : array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'fullName' => $user->getFullName(),
        ];
    }
}
