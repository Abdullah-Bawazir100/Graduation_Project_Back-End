<?php

namespace App\Application\Services;

use Spatie\Activitylog\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogService
{
    public function getWeeklyActivityStatistics(?int $departmentId = null): array
    {
        // أيام الأسبوع بالعربي (السبت = بداية الأسبوع)
        $daysMap = [
            'Saturday'  => 'السبت',
            'Sunday'    => 'الأحد',
            'Monday'    => 'الاثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
        ];

        // حساب بداية ونهاية الأسبوع الحالي (السبت - الجمعة)
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SATURDAY);
        $endOfWeek   = $startOfWeek->copy()->addDays(6)->endOfDay();

        $query = Activity::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->whereIn('log_name', [
                'user', 'department', 'activity_type',
                'payment_type', 'region', 'district', 'job_type',
                'tax_collector', 'tax_payer', 'company', 'charitable_company',
                'tax_type', 'tax_information', 'file', 'file_movement',
                'request', 'file_status', 'address', 'attachment', 'notification'
            ]);

        if ($departmentId !== null) {
            $userIds = \App\Infrastructure\Persistence\Eloquent\Models\UserModel::where('department_id', $departmentId)->pluck('id');
            $query->whereIn('causer_id', $userIds);
        }

        $activities = $query->select(
                DB::raw("DAYNAME(created_at) as day_name"),
                DB::raw("DATE(created_at) as date"),
                'event',
                DB::raw("COUNT(*) as count")
            )
            ->groupBy('day_name', 'date', 'event')
            ->get();

        $result = [];
        foreach ($daysMap as $englishDay => $arabicDay) {
            $dayDate = $startOfWeek->copy();
            while ($dayDate->format('l') !== $englishDay) {
                $dayDate->addDay();
            }

            $dayActivities = $activities->where('day_name', $englishDay);

            $created  = $dayActivities->where('event', 'created')->sum('count');
            $updated  = $dayActivities->where('event', 'updated')->sum('count');
            $deleted  = $dayActivities->where('event', 'deleted')->sum('count');
            $total    = $created + $updated + $deleted;

            $result[] = [
                'day'      => $arabicDay,
                'date'     => $dayDate->format('Y-m-d'),
                'created'  => (int) $created,
                'updated'  => (int) $updated,
                'deleted'  => (int) $deleted,
                'total'    => (int) $total,
            ];
        }

        return [
            'week_start' => $startOfWeek->format('Y-m-d'),
            'week_end'   => $endOfWeek->format('Y-m-d'),
            'days'       => $result,
            'week_total' => array_sum(array_column($result, 'total')),
        ];
    }

    public function getActivities($filters = [])
    {
        $query = Activity::with('causer')
            ->whereIn('log_name', ['user', 'department','activity_type',
                        'payment_type' , 'region' , 'district' , 'job_type',
                        'tax_collector' , 'tax_payer' , 'company' , 'charitable_company'
                        , 'tax_type' , 'tax_information' , 'file' , 'file_movement',
                        'attachment' , 'notification']);

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

        $activities = $query
            ->orderBy('created_at', 'desc')->orderBy('id')
            ->limit(100)
            ->get();
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

            'إنشاء نوع وظيفة' => "تم إنشاء نوع وظيفة جديد." . ($new['name'] ?? ''),
            'تحديث نوع وظيفة' => "تم تحديث نوع وظيفة." . ($new['name'] ?? ''),
            'حذف نوع وظيفة'  => "تم حذف نوع وظيفة." . ($old['name'] ?? ''),

            'إنشاء مأمور' => "تم إنشاء مأمور جديد." . ($new['name'] ?? ''),
            'تحديث مأمور' => "تم تحديث مأمور." . ($new['name'] ?? ''),
            'حذف مأمور'  => "تم حذف مأمور." . ($old['name'] ?? ''),

            'إنشاء مكلف' => "تم إنشاء مكلف جديد." . ($new['name'] ?? ''),
            'تحديث مكلف' => "تم تحديث مكلف." . ($new['name'] ?? ''),
            'حذف مكلف'  => "تم حذف مكلف." . ($old['name'] ?? ''),

            'إنشاء مكلف مع ملف شركة' => "تم إنشاء مكلف  مع ملف شركة جديد." . ($new['name'] ?? ''),
            'تحديث مكلف مع ملف شركة' => "تم تحديث مكلف مع ملف شركة" . ($new['name'] ?? ''),
            'حذف مكلف مع ملف شركة'  => "تم حذف مكلف مع ملف شركة." . ($old['name'] ?? ''),

            'إنشاء مكلف مع ملف شركة خيرية' => "تم إنشاء مكلف  مع ملف شركة خيرية جديد." . ($new['name'] ?? ''),
            'تحديث مكلف مع ملف  شركة خيرية' => "تم تحديث مكلف مع ملف خيرية شركة" . ($new['name'] ?? ''),
            'حذف مكلف مع ملف شركة خيرية'  => "تم حذف مكلف مع ملف خيرية شركة." . ($old['name'] ?? ''),

            'إنشاء نوع ضريبة' => "تم إنشاء نوع ضريبة جديد." . ($new['name'] ?? ''),
            'تحديث نوع ضريبة' => "تم تحديث نوع ضريبة." . ($new['name'] ?? ''),
            'حذف نوع ضريبة'  => "تم حذف نوع ضريبة." . ($old['name'] ?? ''),

            'إنشاء معلومة ضريبية' => "تم إنشاء معلومة ضريبية جديدة." . ($new['name'] ?? ''),
            'تحديث معلومة ضريبية' => "تم تحديث معلومة ضريبية." . ($new['name'] ?? ''),
            'حذف معلومة ضريبية'  => "تم حذف معلومة ضريبية." . ($old['name'] ?? ''),

            'إنشاء ملف' => "تم إنشاء ملف." . ($new['name'] ?? ''),
            'تحديث ملف' => "تم تحديث ملف." . ($new['name'] ?? ''),
            'حذف ملف'  => "تم حذف ملف." . ($old['name'] ?? ''),

            'إنشاء حركة ملف' => "تم إنشاء حركة ملف." . ($new['name'] ?? ''),
            'تحديث حركة ملف' => "تم تحديث حركة ملف." . ($new['name'] ?? ''),
            'حذف حركة ملف'  => "تم حذف حركة ملف." . ($old['name'] ?? ''),

            'إنشاء طلب' => "تم إنشاء طلب" . ($new['name'] ?? ''),
            'تحديث طلب' => "تم تحديث طلب" . ($new['name'] ?? ''),
            'حذف طلب'  => "تم حذف طلب" . ($old['name'] ?? ''),

            'إنشاء مرفق ملف' => "تم إنشاء مرفق ملف" . ($new['name'] ?? ''),
            'تحديث مرفق ملف' => "تم تحديث مرفق ملف" . ($new['name'] ?? ''),
            'حذف مرفق ملف'  => "تم حذف مرفق ملف" . ($old['name'] ?? ''),

            'إنشاء إشعار' => "تم إنشاء إشعار" . ($new['name'] ?? ''),
            'تحديث إشعار' => "تم إشعار" . ($new['name'] ?? ''),
            'حذف إشعار'  => "تم إشعار" . ($old['name'] ?? ''),

            default => $description,
        };
    }
}
