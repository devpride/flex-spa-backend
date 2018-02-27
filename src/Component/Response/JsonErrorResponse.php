<?php
declare(strict_types=1);

namespace App\Component\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonErrorResponse extends JsonResponse
{
    /**
     * @param string $message
     * @param int    $code
     * @param int    $status
     * @param array  $headers
     * @param array  $meta
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $message,
        int $code = 0,
        int $status = JsonResponse::HTTP_BAD_REQUEST,
        array $headers = [],
        array $meta = []
    ) {
        if (empty($message)) {
            throw new \InvalidArgumentException('message must be not empty');
        } elseif ($code < 0) {
            throw new \InvalidArgumentException('code must be not negative');
        } elseif ($status < 100) {
            throw new \InvalidArgumentException('status must be gte 100');
        }

        $data = [
            'error' => [
                'code' => $code,
                'message' => $message,
                'meta' => $meta,
            ],
        ];

        parent::__construct($data, $status, $headers);
    }
}
