<?php
declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFactory
 */
class UserFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param ObjectManager                $objectManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(ObjectManager $objectManager, UserPasswordEncoderInterface $encoder)
    {
        $this->objectManager = $objectManager;
        $this->encoder = $encoder;
    }

    /**
     * @param string $email
     * @param string $username
     * @param string $fullName
     * @param string $plainPassword
     *
     * @return User
     */
    public function create(string $email, string $username, string $fullName, string $plainPassword) : User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setFullName($fullName);
        $user->setPassword($this->encoder->encodePassword($user, $plainPassword));
        $user->setRoles(['ROLE_USER']);

        $this->objectManager->persist($user);
        $this->objectManager->flush();

        return $user;
    }
}
