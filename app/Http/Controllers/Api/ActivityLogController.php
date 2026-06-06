<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\ActivityLogService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request , ActivityLogService $service)
    {
        $user = Auth::user();
        $data = $service->getActivities($request->all());
        $statistics = $service->getActivitySummary(
            $user->role === \App\Domain\User\Enums\UserRole::Admin ? null : $user->department_id
        );

        return response()->json([
            'statistics' => $statistics,
            'data' => ActivityLogResource::collection($data),
        ]);
    }
}
