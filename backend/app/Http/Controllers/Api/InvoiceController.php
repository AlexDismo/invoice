<?php

namespace App\Http\Controllers\Api;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceStatusRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Invoice::query()->orderByDesc('created_at');

        if ($request->boolean('paginate', false)) {
            return InvoiceResource::collection(
                $query->paginate($request->integer('per_page', 15))
            );
        }

        return InvoiceResource::collection($query->get());
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = Invoice::query()->create([
            ...$request->validated(),
            'status' => $request->enum('status', InvoiceStatus::class) ?? InvoiceStatus::Pending,
        ]);

        return (new InvoiceResource($invoice))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): InvoiceResource
    {
        $invoice->fill($request->validated());
        $invoice->recalculateGross();
        $invoice->save();

        return new InvoiceResource($invoice);
    }

    public function changeStatus(UpdateInvoiceStatusRequest $request, Invoice $invoice): InvoiceResource
    {
        $invoice->status = $request->enum('status', InvoiceStatus::class);
        $invoice->save();

        return new InvoiceResource($invoice);
    }
}
