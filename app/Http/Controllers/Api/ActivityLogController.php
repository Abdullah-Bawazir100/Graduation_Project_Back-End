<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;
use Illuminate\Support\Carbon;


class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::with('causer')
            ->whereIn('log_name', ['user', 'department' , 'activity_type'])
            ->orderBy('created_at', 'Asc')
            ->orderBy('id', 'Asc')
            ->get();

        $data = $activities->map(function ($activity) {

            $new = $activity->properties['attributes'] ?? [];
            $old = $activity->properties['old'] ?? [];

            if (isset($new['department_id'])) {
                $new['department'] = optional(DepartmentModel::find($new['department_id']))->name;
            }

            if (isset($old['department_id'])) {
                $old['department'] = optional(DepartmentModel::find($old['department_id']))->name;
            }

            $details = match($activity->description) {
                'إنشاء مستخدم' => "تم إنشاء مستخدم جديد",
                'تحديث مستخدم' => "تم تحديث بيانات المستخدم",
                'حذف مستخدم' => "تم حذف المستخدم",

                'إنشاء قسم' => "تم إنشاء قسم جديد" . ($new['name'] ?? ''),
                'تحديث قسم' => "تم تحديث قسم" . ($new['name'] ?? ''),
                'حذف قسم' => "تم حذف قسم" . ($old['name'] ?? ''),

                'إنشاء نوع نشاط' => "تم إنشاء نوع نشاط جديد" . ($new['name'] ?? ''),
                'تحديث نوع نشاط' => "تم تحديث نوع نشاط" . ($new['name'] ?? ''),
                'حذف نوع نشاط' => "تم حذف نوع نشاط" . ($old['name'] ?? ''),
                default => $activity->description
            };

            return [
                'id' => $activity->id,
                'user' => [
                    'first_name' => optional($activity->causer)->first_name,
                    'last_name' => optional($activity->causer)->last_name,
                    'name' => optional($activity->causer)->user_name ?: 'نظام',
                    'role' => optional($activity->causer)->role?->value ?? '—',
                ],
                'action' => $activity->description,
                'model' => [
                    'id' => $activity->subject_id,
                    'name' => class_basename($activity->subject_type),
                ],
                'details' => $details,
                'datetime' => Carbon::parse($activity->created_at)
                    ->timezone(config('app.timezone'))
                    ->format('Y-m-d h:i:s A'),
                    ];
                });

        return response()->json($data);
    }
}
