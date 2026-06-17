<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNotificationRequest;
use App\Http\Resources\ReportResource;
use App\Jobs\SendCustomerNotificationJob;
use App\Models\Customer;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function send(SendNotificationRequest $request, Customer $customer, ReportService $reportService): JsonResponse
    {


        if ($customer->payment_status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Notifications are only sent for customers with pending payments.',
            ], 422);
        }

        SendCustomerNotificationJob::dispatch($customer, $request->user(), $request->input('type'));

        return response()->json([
            'message' => 'Notification sent successfully',
            'report' => $reportService->summary(),
        ]);
    }
}
