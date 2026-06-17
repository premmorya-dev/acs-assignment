<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function summary(ReportService $reportService): JsonResponse
    {
        return response()->json([
            'report' => $reportService->summary(),
        ]);
    }
}
