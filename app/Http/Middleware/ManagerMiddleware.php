<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domain\User\Enums\UserRole;
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
                'message' => 'الوصول ممنوع ، المدير فقط .',
                'status' => 403,
            ], 403);
        }

        if ($request->isMethod('delete')) {

            $routeName = $request->route()->getName();
            $messages = [
                'manager-users.destroy' => 'لا يمكن للمدير حذف المستخدمين.',
                'manager-departments.destroy' => 'لا يمكن للمدير حذف الأقسام.',
                'manager-activity_types.destroy' => 'لا يمكن للمدير حذف نوع النشاط.',
                'manager-payment_types.destroy' => 'لا يمكن للمدير حذف نوع الدفع.',
                'manager-regions.destroy' => 'لا يمكن للمدير حذف المناطق.',
            ];


            return response()->json([
                'message' => $messages[$routeName] ?? 'عملية الحذف غير مسموحة للمدير.',
                'status' => 403,
            ], 403);
        }


        return $next($request);
    }
}
