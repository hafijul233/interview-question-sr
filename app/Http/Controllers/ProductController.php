<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductIndexRequest;
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
/*            ->toSql();
        dd($productPaginated);*/
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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Models\Product $product
     * @return Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
