<?php
declare(strict_types=1);

namespace App\Consumer;

use App\Entity\Post;
use App\Entity\User;
use App\Utils\Slugger;
use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class PostConsumer
 */
class PostConsumer implements ConsumerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function execute(AMQPMessage $msg)
    {
        $messageBody = unserialize($msg->getBody());

        $user = $this->em->find(User::class, 1);

        $post = new Post();
        $post->setTitle($messageBody['title'] ?? 'Unknown title');
        $post->setContent($messageBody['content'] ?? 'Empty content');
        $post->setSlug(Slugger::slugify($post->getTitle()));
        $post->setSummary('summary');
        $post->setAuthor($user);

        $this->em->persist($post);
        $this->em->flush();

        return true;
    }
}

