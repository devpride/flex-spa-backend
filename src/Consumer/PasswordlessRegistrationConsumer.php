<?php
declare(strict_types=1);

namespace App\Consumer;

use App\Service\Registration\PasswordlessRegistrationDto;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class PasswordlessRegistrationConsumer
 */
class PasswordlessRegistrationConsumer implements ConsumerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     * @param LoggerInterface   $logger
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        try {
            /** @var PasswordlessRegistrationDto $dto */
            $dto = unserialize($msg->getBody());

            $message = (new \Swift_Message('Welcome to FlexSPA!'))
                ->setFrom('info@flex-spa.dev')
                ->setTo($dto->getEmail())
                ->setBody(
                    $this->twig->render(
                        'emails/passwordless-registration.html.twig',
                        ['password' => $dto->getGeneratedPassword()]
                    ),
                    'text/html'
                );

            $this->mailer->send($message);
        } catch (\Throwable $t) {
            $this->logger->error('Mailing with generated password failed.', ['exception' => $t]);

            return false;
        }

        return true;
    }
}

