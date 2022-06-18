<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Product;
use App\Models\Variant;
use App\Services\ProductService;
use App\Services\VariantService;
use App\Support\Constant;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var VariantService
     */
    private $variantService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     * @param VariantService $variantService
     */
    public function __construct(ProductService $productService,
                                VariantService $variantService)
    {
        $this->productService = $productService;
        $this->variantService = $variantService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(ProductIndexRequest $productIndexRequest)
    {
        $filters = $productIndexRequest->validated();

        $productPaginated = $this->productService
            ->listProduct($filters)
            ->orderBy('products.id')
            ->paginate(Constant::ITEM_PER_PAGE);

        $variants = $this->variantService->listVariant()->get();

        return view('products.index', compact('productPaginated', 'variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductStoreRequest $productStoreRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ProductStoreRequest $productStoreRequest)
    {
        $productCreateResponse = $this->productService
            ->createProduct(
                $productStoreRequest->validated()
            );
        return response()->json($productCreateResponse);

    }


    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return void
     * @throws \Exception
     */
    public function show(Product $product)
    {
        $product = $this->productService->showProductById($product->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return Application|Factory|View
     * @throws \Exception
     */
    public function edit(Product $product)
    {
        $product = $this->productService->showProductById($product->id);
        $variants = Variant::all();
        return view('products.edit', compact('variants', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $productUpdateRequest
     * @param Product $product
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(ProductUpdateRequest $productUpdateRequest, Product $product): JsonResponse
    {

        $productUpdateResponse = $this->productService
            ->updateProduct(
                $product->id,
                $productUpdateRequest->validated()
            );
        return response()->json($productUpdateResponse);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
