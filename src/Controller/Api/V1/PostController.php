<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\Api\Component\Response\SuccessResponse;
use App\Service\Tracking\Statsd\Client\DefaultClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function list(int $page, PostRepository $repository, DefaultClient $client) : Response
    {
        $client->increment('PostController.list', 1);

        return new SuccessResponse($repository->findLatest($page)->getIterator()->getArrayCopy());
    }

    /**
     * @Route(path="/search/{query}", name="post_search_by_query")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @param string $query
     *
     * @return Response
     */
    public function search(string $query) : Response
    {
        $results = $this->container->get('fos_elastica.finder.app.post')->find($query);

        return new SuccessResponse(
            array_map(
                function (Post $result) {
                    return [
                        'title' => $result->getTitle(),
                        'tags' => array_map('strval', $result->getTags()->getValues()),
                        'author' => (string) $result->getAuthor(),
                    ];
                },
                $results
            )
        );
    }

    /**
     * @Route(path="/", name="post_create")
     * @Method("POST")
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request) : Response
    {
        $message = [
            'title' => $request->get('title'),
            'content' => $request->get('content'),
        ];

        $this->container->get('old_sound_rabbit_mq.post_producer')->publish(serialize($message));

        return new SuccessResponse();
    }

    /**
     * @Route(path="/mail", name="post_mail")
     * @Method("GET")
     * @param Request       $request
     * @param \Swift_Mailer $mailer
     *
     * @return Response
     */
    public function mail(Request $request, \Swift_Mailer $mailer) : Response
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('info@flexspa.com')
            ->setTo('elijah.zakirov@gmail.com')
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    ['name' => $request->get('name', 'John Doe')]
                ),
                'text/html'
            );

        $mailer->send($message);

        return new SuccessResponse();
    }
}

