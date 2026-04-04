<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiResponse;
use App\Domain\User\Enums\UserRole;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->role !== UserRole::Admin) {
            return ApiResponse::forbidden([] , 'Access denied. Admins only.')->toResponse($request);
        }

        return $next($request);
    }
}
