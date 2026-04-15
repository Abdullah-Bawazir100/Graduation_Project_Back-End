<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\ActivityLogService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use Illuminate\Http\Request;
class ActivityLogController extends Controller
{
    public function index(Request $request , ActivityLogService $service)
    {
        $data = $service->getActivities($request->all());

        return ActivityLogResource::collection($data);
    }
}
