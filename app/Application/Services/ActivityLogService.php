<?php

namespace App\Application\Services;

use Spatie\Activitylog\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;

class ActivityLogService
{
    public function getActivities($filters = [])
    {
        $query = Activity::with('causer')
            ->whereIn('log_name', ['user', 'department','activity_type',
                        'payment_type' , 'region' , 'district']);

        // Filters
        if (!empty($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        $activities = $query->orderBy('id' , 'Asc')->limit(100)->get();

        return $this->transform($activities);
    }

    private function transform($activities)
    {
        $departments = $this->getDepartments($activities);

        return $activities->map(function ($activity) use ($departments) {

            $new = $activity->properties['attributes'] ?? [];
            $old = $activity->properties['old'] ?? [];

            if (isset($new['department_id'])) {
                $new['department'] = $departments[$new['department_id']] ?? null;
            }

            if (isset($old['department_id'])) {
                $old['department'] = $departments[$old['department_id']] ?? null;
            }

            return [
                'id' => $activity->id,

                'user' => [
                    'first_name' => optional($activity->causer)->first_name,
                    'last_name'  => optional($activity->causer)->last_name,
                    'name'       => optional($activity->causer)->user_name ?? 'نظام',
                    'role'       => optional($activity->causer)->role?->value ?? '—',
                ],

                'action' => $activity->description,

                'model' => [
                    'id'   => $activity->subject_id,
                    'name' => class_basename($activity->subject_type),
                ],

                'details' => $this->formatDetails($activity->description, $new, $old),

                'datetime' => $activity->created_at
                    ->timezone(config('app.timezone'))
                    ->format('Y-m-d h:i:s A'),
            ];
        });
    }

    private function getDepartments($activities)
    {
        $ids = $activities->flatMap(function ($activity) {
            return [
                $activity->properties['attributes']['department_id'] ?? null,
                $activity->properties['old']['department_id'] ?? null,
            ];
        })->filter()->unique();

        return DepartmentModel::whereIn('id', $ids)
            ->pluck('name', 'id');
    }

    private function formatDetails($description, $new, $old)
    {
        $description = trim($description);

        return match ($description) {

            'إنشاء مستخدم' => "تم إنشاء مستخدم جديد",
            'تحديث مستخدم' => "تم تحديث بيانات المستخدم",
            'حذف مستخدم'  => "تم حذف المستخدم",

            'إنشاء قسم' => "تم إنشاء قسم جديد." . ($new['name'] ?? ''),
            'تحديث قسم' => "تم تحديث قسم." . ($new['name'] ?? ''),
            'حذف قسم'   => "تم حذف قسم." . ($old['name'] ?? ''),

            'إنشاء نوع نشاط' => "تم إنشاء نوع نشاط جديد." . ($new['name'] ?? ''),
            'تحديث نوع نشاط' => "تم تحديث نوع نشاط." . ($new['name'] ?? ''),
            'حذف نوع نشاط'  => "تم حذف نوع نشاط." . ($old['name'] ?? ''),

            'إنشاء نوع دفع' => "تم إنشاء نوع دفع جديد." . ($new['name'] ?? ''),
            'تحديث نوع دفع' => "تم تحديث نوع دفع." . ($new['name'] ?? ''),
            'حذف نوع دفع'  => "تم حذف نوع دفع." . ($old['name'] ?? ''),

            'إنشاء منطقة' => "تم إنشاء منطقة جديدة." . ($new['name'] ?? ''),
            'تحديث منطقة' => "تم تحديث منطقة." . ($new['name'] ?? ''),
            'حذف منطقة'  => "تم حذف منطقة." . ($old['name'] ?? ''),

            'إنشاء حي' => "تم إنشاء حي جديد." . ($new['name'] ?? ''),
            'تحديث حي' => "تم تحديث حي." . ($new['name'] ?? ''),
            'حذف حي'  => "تم حذف حي." . ($old['name'] ?? ''),

            default => $description,
        };
    }
}
