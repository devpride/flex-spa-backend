<?php
declare(strict_types=1);

namespace App\Service\Creation\Post;

use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Utils\Slugger;
use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PostCreator
 */
class PostCreator
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
     * @return PostCreator
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param PostDto $dto
     *
     * @return bool
     */
    public function createAsync(PostDto $dto) : bool
    {
        try {
            $this->producer->publish(serialize($dto));

            return true;
        } catch (\Throwable $t) {
            if (!is_null($this->logger)) {
                $this->logger->warning('Post creation failed.', ['exception' => $t]);
            }
        }

        return false;
    }

    /**
     * @param PostDto $dto
     *
     * @return bool
     */
    public function create(PostDto $dto) : bool
    {
        try {
            $author = $this->objectManager->find(User::class, $dto->getAuthorId());

            if (is_null($author)) {
                throw new \DomainException(sprintf('User with id "%d" does not exist.', $dto->getAuthorId()));
            }

            $post = new Post();
            $post->setTitle($dto->getTitle());
            $post->setSummary($dto->getSummary());
            $post->setContent($dto->getContent());
            $post->setPublishedAt(new \DateTime($dto->getPublishedAt()->format('Y-m-d H:i:s')));
            $post->setSlug(Slugger::slugify($dto->getTitle()));
            $post->setAuthor($author);

            $tags = [];
            foreach ($dto->getTags() as $tagName) {
                $tag = $this->objectManager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);

                if (is_null($tag)) {
                    $tag = new Tag();
                    $tag->setName($tagName);

                    $this->objectManager->persist($tag);
                }

                $tags[] = $tag;
            }
            $post->addTag(...$tags);

            $this->objectManager->persist($post);
            $this->objectManager->flush();

            return true;
        } catch (\Throwable $t) {
            if (!is_null($this->logger)) {
                $this->logger->warning('Post creation failed.', ['exception' => $t]);
            }
        }

        return false;
    }
}

