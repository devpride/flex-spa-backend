<?php
declare(strict_types=1);

namespace App\Service\Security;

/**
 * Class PasswordGenerator
 */
class PasswordGenerator
{
    /**
     * @var int
     */
    private $minLength = 6;

    /**
     * @var int
     */
    private $maxLength = 8;

    /**
     * @param int $minLength
     *
     * @return PasswordGenerator
     */
    public function setMinLength(int $minLength) : PasswordGenerator
    {
        $this->minLength = $minLength;

        return $this;
    }

    /**
     * @param int $maxLength
     *
     * @return PasswordGenerator
     */
    public function setMaxLength(int $maxLength) : PasswordGenerator
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * @return string
     */
    public function generate() : string
    {
        return base64_encode(random_bytes(random_int($this->minLength, $this->maxLength)));
    }
}
