<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Http\Requests\UploadCsvRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CsvImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
   public function index(Request $request): CustomerCollection
{
    $query = Customer::query();

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('email')) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('phone_number')) {
        $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
    }

    $perPage = min((int)$request->input('per_page', 20), 100);

    $customers = $query
        ->latest()
        ->paginate($perPage);

    return new CustomerCollection($customers);
}

    public function updatePaymentStatus(UpdatePaymentStatusRequest $request, Customer $customer): CustomerResource
    {
       
        $customer->payment_status = $request->input('payment_status');
        $customer->save();

        return new CustomerResource($customer);
    }

    public function uploadCsv(UploadCsvRequest $request, CsvImportService $importService): JsonResponse
    {
        $result = $importService->import($request->file('file'));

        return response()->json($result);
    }
}
