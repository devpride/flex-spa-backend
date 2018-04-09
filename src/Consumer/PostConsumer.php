<?php
declare(strict_types=1);

namespace App\Consumer;

use App\Entity\Post;
use App\Entity\User;
use App\Service\Creation\Post\PostCreator;
use App\Service\Creation\Post\PostDto;
use App\Utils\Slugger;
use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class PostConsumer
 */
class PostConsumer implements ConsumerInterface
{
    /**
     * @var PostCreator
     */
    private $creator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PostCreator     $creator
     * @param LoggerInterface $logger
     */
    public function __construct(PostCreator $creator, LoggerInterface $logger)
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
            /** @var PostDto $postDto */
            $postDto = unserialize($msg->getBody());
            $this->creator->create($postDto);
        } catch (\Throwable $t) {
            $this->logger->error('Post creation consume failed.', ['exception' => $t]);

            return false;
        }

        return true;
    }
}

