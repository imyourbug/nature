<?php

namespace App\Http\Controllers;

use App\Constant\GlobalConstant;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Http\Requests\CreateRFQRequest;
use App\Models\Product;
use App\Models\RFQ;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RFQController extends Controller
{
    /**
     * Display a listing of RFQs.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $vendorId = $request->input('vendor_id');
        $status = $request->input('status');

        $rfqs = RFQ::with(['product', 'vendor'])
            ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->get();

        return $this->respondSuccess($rfqs);
    }

    /**
     * Store a newly created RFQ.
     *
     * @param CreateRFQRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateRFQRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Get product to determine vendor_id
        $product = Product::find($validated['product_id']);

        if (!$product) {
            throw new NotFoundException('Sản phẩm không tồn tại');
        }

        // Set vendor_id from product
        $validated['vendor_id'] = $product->vendor_id;

        if (!$validated['vendor_id']) {
            throw new BadRequestException('Sản phẩm chưa có nhà bán');
        }

        $rfq = RFQ::create($validated);

        return $this->respondSuccess(
            $rfq->load(['product', 'vendor']),
            'RFQ đã được tạo thành công',
            201
        );
    }

    /**
     * Accept an RFQ.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function accept(int $id): JsonResponse
    {
        $rfq = RFQ::with(['product', 'vendor'])->find($id);

        if (!$rfq) {
            throw new NotFoundException('RFQ không tồn tại');
        }

        if ($rfq->status !== GlobalConstant::RFQ_STATUS_PENDING) {
            throw new BadRequestException('RFQ này đã được xử lý');
        }

        $rfq->update(['status' => GlobalConstant::RFQ_STATUS_ACCEPTED]);

        return $this->respondSuccess(
            $rfq->fresh(['product', 'vendor']),
            'RFQ đã được chấp nhận thành công'
        );
    }
}
