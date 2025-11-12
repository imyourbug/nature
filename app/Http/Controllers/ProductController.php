<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $brand = $request->input('brand');
        $status = $request->input('status');
        $limit = (int) $request->input('limit', 10);
        $pagination = $request->has('pagination');

        $products = Product::with('vendor')
            ->when($brand, fn($q) => $q->where('brand', $brand))
            ->when($status, fn($q) => $q->where('status', $status))
            ->get();

        return $pagination ?
            $this->respondWithPagination($products->paginate($limit))
            : $this->respondSuccess($products);
    }

    /**
     * Store a newly created product.
     *
     * @param CreateProductRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return $this->respondSuccess(
            $product->load('vendor'),
            'Sản phẩm đã được tạo thành công',
            201
        );
    }
}
