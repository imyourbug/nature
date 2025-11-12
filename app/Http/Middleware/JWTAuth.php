<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTAuth
{
    private const DEFAULT_TOKEN = 'admin-token';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     *
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            throw new UnauthorizedException('Token không được cung cấp');
        }

        // Mock JWT authentication - chỉ chấp nhận "admin-token"
        if ($token !== self::DEFAULT_TOKEN) {
            throw new UnauthorizedException('Token không hợp lệ');
        }

        return $next($request);
    }
}
