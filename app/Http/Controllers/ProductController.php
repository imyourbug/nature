<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    private const CACHE_TTL = 5;

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
        $page = (int) $request->input('pagination', 1);

        // Build cache key by query params and pagination state
        $cacheKey = sprintf(
            'products:%s',
            md5(json_encode([
                'brand' => $brand,
                'status' => $status,
                'limit' => $limit,
                'page' => $page,
            ]))
        );

        // Check if cache data exists
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            return $this->respondSuccess($cached['data'], null, 200, $cached['meta']);
        }

        // Get products from database
        $paginator = Product::with('vendor')
            ->when($brand, static fn($builder) => $builder->where('brand', $brand))
            ->when($status, static fn($builder) => $builder->where('status', $status))
            ->paginate($limit, ['*'], 'page', $page);

        $paginatorArray = $paginator->toArray();
        $meta = $paginatorArray;
        unset($meta['data']);

        $payload = [
            'data' => $paginatorArray['data'],
            'meta' => $meta,
        ];

        // Save to cache
        Cache::put($cacheKey, $payload, now()->addMinutes(self::CACHE_TTL));

        return $this->respondSuccess($payload['data'], null, 200, $payload['meta']);
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
