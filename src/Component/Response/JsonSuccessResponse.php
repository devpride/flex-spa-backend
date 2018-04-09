<?php
declare(strict_types=1);

namespace App\Component\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JsonSuccessResponse
 */
class JsonSuccessResponse extends JsonResponse
{
    /**
     * @param null  $data
     * @param int   $status
     * @param array $headers
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($data = null, int $status = JsonResponse::HTTP_OK, array $headers = [])
    {
        if (is_resource($data)) {
            throw new \InvalidArgumentException('Data cant be of resource type');
        } elseif ($status < 100) {
            throw new \InvalidArgumentException('status must be gte 100');
        }

        $data = [
            'success' => true,
            'data' => $data
        ];

        parent::__construct($data, $status, $headers);
    }

}
