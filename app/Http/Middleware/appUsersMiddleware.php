<?php

namespace App\Http\Middleware;

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

        if (!$user) {
            return response()->json([
                'message' => 'غير مصرح.',
                'status' => 403,
            ], 403);
        }


        if($user->role === UserRole::Admin)
        {
            return $next($request);
        }

        if($user->role === UserRole::Manager && $request->isMethod('delete'))
        {
            $routeName = $request->route()->getName();
            $messages = [
                'app_users.destroy' => 'لا يمكن للمدير حذف المستخدمين.',
                'departments.destroy' => 'لا يمكن للمدير حذف الأقسام.',
                'activity_types.destroy' => 'لا يمكن للمدير حذف نوع النشاط.',
                'payment_types.destroy' => 'لا يمكن للمدير حذف نوع الدفع.',
                'regions.destroy' => 'لا يمكن للمدير حذف المناطق.',
                'districts.destroy' => 'لا يمكن للمدير حذف الحي.',
                'tax-collectors.destroy' =>  'لا يمكن للمدير حذف جامع الضرائب.',
                'tax-payers.destroy' =>  'لا يمكن للمدير حذف دافع الضرائب.',
            ];


            return response()->json([
                'message' => $messages[$routeName] ?? 'عملية الحذف غير مسموحة للمدير.',
                'status' => 403,
            ], 403);
        }

        if($user->role === UserRole::Employee && ($request->isMethod('delete')
            || $request->isMethod('put') || $request->isMethod('patch')))
        {
            return response()->json([
                'message' => 'غير مصرح ، المشرف و المدير فقط يمكنهم (التحديث - الحذف).',
                'status' => 403,
            ], 403);
        }

        if($user->role === UserRole::Collectors_Manager)
        {
            $routeName = $request->route()->getName();

            if(($routeName === 'tax-collectors.store' && $request->isMethod('post'))
                || ($routeName === 'tax-collectors.update' && ($request->isMethod('put') || $request->isMethod('patch')))
                || (($routeName === 'tax-collectors.index' || $routeName === 'tax-collectors.show') && ($request->isMethod('get'))))
            {
                return $next($request);
            }

            elseif($routeName !== 'tax-collectors.store')
            {
                return response()->json([
                'message' => 'غير مصرح ، مدير المأمورين ليس لديه صلاحيات على الأقسام الأخرى.',
                'status' => 403,
            ], 403);
            }

            return response()->json([
                'message' => 'غير مصرح ، مدير المأمورين ليس لديه صلاحية (التعديل - الحذف).',
                'status' => 403,
            ], 403);
        }

        if($user->role === UserRole::Tax_Payer)
        {
            $routeName = $request->route()->getName();
            if(($routeName === 'tax-payers.show' || $routeName === 'tax-payers.update'))
            {
                return $next($request);
            }

            else {
                return response()->json([
                'message' => 'غير مصرح : لا يمكن للمكلف التحكم أقسام أخرى من النظام.',
                'status' => 403,
            ], 403);
            }

        }

        return $next($request);

    }
}
