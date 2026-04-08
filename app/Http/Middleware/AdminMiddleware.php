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
        // return response()->json([
        //     'auth_check' => Auth::check(),
        //     'user' => $user,
        //     'role_value' => $user?->role,
        //     'role_type' => gettype($user?->role),
        //     'is_enum' => $user?->role instanceof UserRole,
        //     'expected_role' => UserRole::Admin,
        //     'comparison_result' => $user?->role === UserRole::Admin,
        // ]);

        if (!$user || $user->role !== UserRole::Admin) {
            return response()->json([
                'message' => 'Access denied. Admins only.',
                'status' => 403,
            ], 403);
        }

        return $next($request);

    }
}
