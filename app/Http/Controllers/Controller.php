<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Build a standardized JSON success response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $status
     * @param array $extra
     *
     * @return JsonResponse
     */
    protected function respondSuccess(mixed $data = null, ?string $message = null, int $status = 200, array $extra = []): JsonResponse
    {
        $payload = array_merge(
            ['success' => true],
            $message ? ['message' => $message] : [],
            !is_null($data) ? ['data' => $data] : [],
            $extra
        );

        return response()->json($payload, $status);
    }
}
