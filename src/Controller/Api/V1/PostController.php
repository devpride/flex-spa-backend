<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\PostRepository;
use App\Service\Api\Component\Response\ErrorResponse;
use App\Service\Api\Component\Response\SuccessResponse;
use App\Service\Creation\Post\PostCreator;
use App\Service\Creation\Post\PostDto;
use App\Service\Search\PostFinder;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route(path="v1/posts")
 */
class PostController extends Controller
{
    /**
     * @Route(path="/", defaults={"page":"1"}, name="post_list")
     * @Route(path="/page/{page}", name="post_list_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @param int            $page
     * @param PostRepository $repository
     *
     * @return Response
     */
    public function list(int $page, PostRepository $repository) : Response
    {
        $posts = $repository->findLatest($page)->getIterator()->getArrayCopy();

        return new SuccessResponse(array_map([$this, 'mapPost'], $posts));
    }

    /**
     * @Route(path="/search/{query}", name="post_search_by_query")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @param string     $query
     * @param PostFinder $finder
     *
     * @return Response
     */
    public function search(string $query, PostFinder $finder) : Response
    {
        $results = $finder->find($query);

        return new SuccessResponse(array_map([$this, 'mapPost'], $results));
    }

    /**
     * @Route(path="/", name="post_create")
     * @Method("POST")
     * @param Request               $request
     * @param TokenStorageInterface $tokenStorage
     * @param PostCreator           $creator
     *
     * @return Response
     */
    public function create(Request $request, TokenStorageInterface $tokenStorage, PostCreator $creator) : Response
    {
        if (is_null($request->get('title')) || is_null($request->get('content'))) {
            return new ErrorResponse('Missing required fields: "title", "content".');
        }

        $dto = (new PostDto())
            ->setTitle($request->get('title'))
            ->setSummary($request->get('summary', ''))
            ->setContent($request->get('content'))
            ->setPublishedAt(new \DateTimeImmutable())
            ->setTags(array_map('trim', explode(',', $request->get('tags', ''))))
            ->setAuthorId($tokenStorage->getToken()->getUser()->getId());

        return $creator->createAsync($dto)
            ? new SuccessResponse(null, JsonResponse::HTTP_CREATED)
            : new ErrorResponse('Post creation failed');
    }

    /**
     * @Route(path="/{id}", name="post_delete")
     * @Method("DELETE")
     * @param int                   $id
     * @param ObjectManager         $objectManager
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     */
    public function delete(
        int $id,
        ObjectManager $objectManager,
        TokenStorageInterface $tokenStorage
    ) : Response {
        $post = $objectManager->getRepository(Post::class)->find($id);

        if (is_null($post)) {
            return new ErrorResponse('Post does not exist');
        }

        if ($post->getAuthor()->getId() !== $tokenStorage->getToken()->getUser()->getId()) {
            return new ErrorResponse('You have no permissions to delete this post');
        }

        $objectManager->remove($post);
        $objectManager->flush();

        return new SuccessResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/{id}", name="post_view")
     * @Method("GET")
     * @param int           $id
     * @param ObjectManager $objectManager
     *
     * @return Response
     */
    public function view(
        int $id,
        ObjectManager $objectManager
    ) : Response {
        $post = $objectManager->getRepository(Post::class)->find($id);

        if (is_null($post)) {
            return new ErrorResponse('Post does not exist');
        }

        return new SuccessResponse($this->mapPost($post));
    }

    /**
     * @param Post $post
     *
     * @return array
     */
    private function mapPost(Post $post) : array
    {
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'summary' => $post->getSummary(),
            'content' => $post->getContent(),
            'publishedAt' => $post->getPublishedAt()->format('Y-m-d H:i:s'),
            'tags' => array_map([$this, 'mapTag'], $post->getTags()->getValues()),
            'author' => [
                'id' => $post->getAuthor()->getId(),
                'name' => $post->getAuthor()->getFullName(),
            ],
        ];
    }

    /**
     * @param Tag $tag
     *
     * @return string
     */
    private function mapTag(Tag $tag) : string
    {
        return $tag->getName();
    }
}

