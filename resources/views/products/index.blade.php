@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">

        <form action="{{ route('product.index') }}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control"
                           value="{{ old('title') }}">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="product-variant" class="form-control">

                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From"
                               class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <div class="card-header">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($productPaginated as $product)
                        <tr>
                            <td>{{ $product->id ?? '' }}</td>
                            <td>{{ $product->title ?? '' }}
                                <br> Created at : {{ $product->created_at->diffForHumans() ?? 'N/A' }}
                            </td>
                            <td width="45%">

                                {{ $product->description ?? '' }}
                            </td>
                            <td>
                                @forelse($product->productVariantPrices as $productVariantPrice)
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden;" id="variant">
                                        <dt class="col-sm-3 pb-0">
                                            @if($productVariantPrice->variantOne != null)
                                                {{ $productVariantPrice->variantOne->variant }} /
                                            @endif

                                            @if($productVariantPrice->variantTwo != null)
                                                {{ $productVariantPrice->variantTwo->variant }} /
                                            @endif

                                            @if($productVariantPrice->variantThree != null)
                                                {{ $productVariantPrice->variantThree->variant }}
                                            @endif
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price
                                                    : {{ number_format($productVariantPrice->price,2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock
                                                    : {{ number_format($productVariantPrice->stock,2) }}</dd>
                                            </dl>
                                        </dd>
                                    </dl>
                                @empty
                                    <p>No Data Available</p>
                                @endforelse
                                <button
                                    onclick="$('#variant').toggleClass('h-auto')"
                                    class="btn btn-sm btn-link">
                                    Show more
                                </button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No Product Data Available</td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing 1 to 10 out of 100</p>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    {{ $productPaginated->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
