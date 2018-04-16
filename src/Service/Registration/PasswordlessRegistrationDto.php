<?php
declare(strict_types=1);

namespace App\Service\Registration;

/**
 * Class PasswordlessRegistrationDto
 */
class PasswordlessRegistrationDto implements \Serializable
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $generatedPassword;

    /**
     * @param string $email
     * @param string $generatedPassword
     */
    public function __construct($email, $generatedPassword)
    {
        $this->email = $email;
        $this->generatedPassword = $generatedPassword;
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getGeneratedPassword() : string
    {
        return $this->generatedPassword;
    }

    public function serialize()
    {
        return serialize(
            [
                $this->getEmail(),
                $this->getGeneratedPassword(),
            ]
        );
    }

    public function unserialize($serialized)
    {
        list(
            $this->email,
            $this->generatedPassword
            ) = unserialize($serialized);
    }
}
