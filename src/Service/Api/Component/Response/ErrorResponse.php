<?php
declare(strict_types=1);

namespace App\Service\Api\Component\Response;

use App\Component\Response\JsonErrorResponse;

/**
 * Class ErrorResponse
 */
class ErrorResponse extends JsonErrorResponse
{
    /**
     * Requested unknown API method.
     */
    public const ERROR_API_METHOD_NOT_FOUND = 1;
}
