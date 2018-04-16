<?php
declare(strict_types=1);

namespace App\Service\Registration;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PasswordlessRegistrationMailer
 */
class PasswordlessRegistrationMailer
{
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @param ProducerInterface $producer
     */
    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @param null|LoggerInterface $logger
     *
     * @return PasswordlessRegistrationMailer
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param PasswordlessRegistrationDto $dto
     *
     * @return bool
     */
    public function mailAsync(PasswordlessRegistrationDto $dto) : bool
    {
        try {
            $this->producer->publish(serialize($dto));

            return true;
        } catch (\Throwable $t) {
            if (!is_null($this->logger)) {
                $this->logger->warning('Mailing with generated password failed.', ['exception' => $t]);
            }
        }

        return false;
    }
}
