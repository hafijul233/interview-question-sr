<?php


namespace App\Services;


use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public $product;

    public function __construct()
    {
        $this->product = new Product;
    }

    /**
     * Return a query builder instance with given condition
     *
     * @param array $filters
     * @return Builder
     */
    public function listProduct(array $filters = [])
    {
        $query = $this->product->newQuery();

        if (isset($filters['title']) && $filters['title']) {
            $query->where('title', 'like', "%{$filters['title']}%");
        }

        if (isset($filters['variant']) && $filters['variant']) {
            $query->rightJoin('product_variants', 'products.id', '=', 'product_variants.product_id');
            $query->where('variant', '=', $filters['variant']);
        }

        if ((isset($filters['price_from']) && $filters['price_from'])
            || (isset($filters['price_to']) && $filters['price_to'])) {

            $query->leftJoin('product_variant_prices', 'products.id', '=', 'product_variant_prices.product_id');

            if (isset($filters['price_from']) && $filters['price_from']) {
                $query->where('price', '>=', "%{$filters['price_from']}%");
            }

            if (isset($filters['price_to']) && $filters['price_to']) {
                $query->where('price', '<=', $filters['price_to']);
            }
        }


        if (isset($filters['date']) && $filters['date']) {
            $query->where(DB::raw('DATE(products.created_at)'), '=', $filters['date']);
        }

        return $query;
    }

    public function createProduct(array $inputs = [])
    {

    }


    public function showProductById(array $inputs = [])
    {

    }

    public function updateProduct(array $inputs = [])
    {

    }

}
