<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/v1/posts")
 */
class PostController extends AbstractController
{
    /**
     * @Route(path="/", defaults={"page":"1"}, name="post_list")
     * @Route(path="/page/{page}", name="post_list_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @param int $page
     * @param PostRepository $repository
     *
     * @return Response
     */
    public function list(int $page, PostRepository $repository) : Response
    {
        return $this->json($repository->findLatest($page)->getIterator()->getArrayCopy());
    }
}

