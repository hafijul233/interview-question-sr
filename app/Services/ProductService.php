<?php


namespace App\Services;


use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

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
