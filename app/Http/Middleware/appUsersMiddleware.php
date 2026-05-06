<?php

namespace App\Http\Middleware;

use App\Application\User\Constants\UsersRolesAndAllowedRoutes;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\User\Enums\UserRole;
class appUsersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Check authentication
        if (!$user) {
            return response()->json([
                'message' => 'غير مصرح.',
                'status' => 403,
            ], 403);
        }

        // 2. Ensure role is valid enum
        if (!$user->role instanceof UserRole) {
            return response()->json([
                'message' => 'دور المستخدم غير صالح.',
                'status' => 403,
            ], 403);
        }

        $role = $user->role->value;

        // 3. Ensure route has a name
        $routeName = $request->route()?->getName();
        if (!$routeName) {
            return response()->json([
                'message' => 'Route غير معرف.',
                'status' => 403,
            ], 403);
        }

        // 4. Get allowed routes for the role
        $allowedRoutes = UsersRolesAndAllowedRoutes::$allowedRoutes[$role] ?? [];

        // 5. Authorization check (strict)
        if (!in_array($routeName, $allowedRoutes, true)) {
            return response()->json([
                'message' => $this->getForbiddenMessage($user->role, $request->method(), $routeName),
                'status' => 403,
            ], 403);
        }

        return $next($request);
    }

    private function getForbiddenMessage(UserRole $role, string $method, ?string $routeName): string
    {
        return match ($role) {
            UserRole::Manager =>
                $routeName && str_contains($routeName, 'destroy')
                    ? 'لا يمكن للمدير تنفيذ عمليات الحذف.'
                    : 'غير مصرح.',

            UserRole::Employee =>
                in_array($method, ['DELETE', 'PUT', 'PATCH'])
                    ? 'غير مصرح ، الموظف يمكنه القراءة فقط.'
                    : 'غير مصرح.',

            UserRole::Collectors_Manager =>
                $routeName && str_contains($routeName, 'destroy')
                    ? 'غير مصرح ، لا يمكن لمدير المأمورين الحذف.'
                    : 'غير مصرح.',

            UserRole::Tax_Payer =>
                'غير مصرح : لا يمكن للمكلف الوصول لهذا المسار.',

            default => 'غير مصرح.',
        };
    }
}
