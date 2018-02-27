<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Api\Component\Response\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class RouteNotFoundController
 */
class RouteNotFoundController extends AbstractController
{
    /**
     * @Route("/", name="api_route_not_found")
     * @param Request $request
     *
     * @return ErrorResponse
     */
    public function indexAction(Request $request) : ErrorResponse
    {
        return new ErrorResponse(
            sprintf(
                'API method "%s" not found',
                $request->getRequestUri()
            ),
            ErrorResponse::ERROR_API_METHOD_NOT_FOUND,
            ErrorResponse::HTTP_BAD_REQUEST
        );
    }

}
