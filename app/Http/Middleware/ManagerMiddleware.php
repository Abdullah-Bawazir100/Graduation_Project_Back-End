<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domain\User\Enums\UserRole;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if(!$user || $user->role !== UserRole::Manager) {
            return response()->json([
                'message' => 'Access denied. Manager only.',
                'status' => 403,
            ], 403);
        }

        if ($request->isMethod('delete')) {
            return response()->json([
                'message' => 'Managers cannot delete departments.',
                'status' => 403,
            ], 403);
        }

        return $next($request);
    }
}
