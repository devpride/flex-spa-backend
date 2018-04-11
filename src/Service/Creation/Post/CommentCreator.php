<?php
declare(strict_types=1);

namespace App\Service\Creation\Post;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CommentCreator
 */
class CommentCreator
{
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @param ProducerInterface $producer
     * @param ObjectManager     $objectManager
     */
    public function __construct(ProducerInterface $producer, ObjectManager $objectManager)
    {
        $this->producer = $producer;
        $this->objectManager = $objectManager;
    }

    /**
     * @param null|LoggerInterface $logger
     *
     * @return CommentCreator
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param CommentDto $dto
     *
     * @return bool
     */
    public function createAsync(CommentDto $dto) : bool
    {
        try {
            $this->producer->publish(serialize($dto));

            return true;
        } catch (\Throwable $t) {
            if (!is_null($this->logger)) {
                $this->logger->warning('Comment creation failed.', ['exception' => $t]);
            }
        }

        return false;
    }

    /**
     * @param CommentDto $dto
     *
     * @return bool
     */
    public function create(CommentDto $dto) : bool
    {
        try {
            $author = $this->objectManager->find(User::class, $dto->getAuthorId());
            $post = $this->objectManager->find(Post::class, $dto->getPostId());

            if (is_null($author)) {
                throw new \DomainException(sprintf('User with id "%d" does not exist.', $dto->getAuthorId()));
            }

            if (is_null($post)) {
                throw new \DomainException(sprintf('Post with id "%d" does not exist.', $dto->getPostId()));
            }

            $comment = new Comment();
            $comment->setAuthor($author);
            $comment->setPost($post);
            $comment->setContent($dto->getContent());
            $comment->setPublishedAt(new \DateTime($dto->getPublishedAt()->format('Y-m-d H:i:s')));

            $this->objectManager->persist($comment);
            $this->objectManager->flush();

            return true;
        } catch (\Throwable $t) {
            if (!is_null($this->logger)) {
                $this->logger->warning('Comment creation failed.', ['exception' => $t]);
            }
        }

        return false;
    }
}

