<?php
declare(strict_types=1);

namespace App\Consumer;

use App\Entity\Post;
use App\Entity\User;
use App\Service\Creation\Post\CommentCreator;
use App\Service\Creation\Post\CommentDto;
use App\Service\Creation\Post\PostCreator;
use App\Service\Creation\Post\PostDto;
use App\Utils\Slugger;
use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class CommentConsumer
 */
class CommentConsumer implements ConsumerInterface
{
    /**
     * @var CommentCreator
     */
    private $creator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CommentCreator     $creator
     * @param LoggerInterface $logger
     */
    public function __construct(CommentCreator $creator, LoggerInterface $logger)
    {
        $this->creator = $creator;
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
            /** @var CommentDto $commentDto */
            $commentDto = unserialize($msg->getBody());
            $this->creator->create($commentDto);
        } catch (\Throwable $t) {
            $this->logger->error('Comment creation consume failed.', ['exception' => $t]);

            return false;
        }

        return true;
    }
}

