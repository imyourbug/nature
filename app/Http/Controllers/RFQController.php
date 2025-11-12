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
     *
     * @throws NotFoundException|BadRequestException
     */
    public function store(CreateRFQRequest $request): JsonResponse
    {
        $product = Product::find($request->input('product_id'));
        $vendorId = $product->vendor_id;

        if (!$vendorId) {
            throw new BadRequestException('Sản phẩm chưa có nhà bán');
        }

        $rfq = RFQ::create([
            'product_id' => $request->input('product_id'),
            'vendor_id' => $vendorId,
            'quantity' => $request->input('quantity'),
        ]);

        return $this->respondSuccess(
            $rfq->load(['product', 'vendor']),
            'RFQ đã được tạo thành công',
            201
        );
    }

    /**
     * Accept a RFQ.
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws NotFoundException|BadRequestException
     */
    public function accept(int $id): JsonResponse
    {
        $rfq = RFQ::find($id);

        if (!$rfq) {
            throw new NotFoundException('RFQ không tồn tại');
        }

        if ($rfq->status !== GlobalConstant::RFQ_STATUS_PENDING) {
            throw new BadRequestException('RFQ này đã được xử lý');
        }

        $rfq->update(['status' => GlobalConstant::RFQ_STATUS_ACCEPTED]);

        return $this->respondSuccess(
            $rfq->refresh(),
            'RFQ đã được chấp nhận thành công'
        );
    }
}
