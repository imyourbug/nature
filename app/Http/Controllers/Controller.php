<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    /**
     * Build a standardized JSON response with pagination meta.
     *
     * @param LengthAwarePaginator $paginator
     * @param string|null $message
     * @param int $status
     *
     * @return JsonResponse
     */
    protected function respondWithPagination(LengthAwarePaginator $paginator, ?string $message = null, int $status = 200): JsonResponse
    {
        $pagination = [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];

        return $this->respondSuccess($paginator->items(), $message, $status, $pagination);
    }
}
