<?php


namespace App\Services;


use App\Models\Variant;
use Illuminate\Database\Eloquent\Builder;

class VariantService
{
    public $variant;

    public function __construct()
    {
        $this->variant = new Variant;
    }

    /**
     * Return a query builder instance with given condition
     *
     * @param array $filters
     * @return Builder
     */
    public function listVariant(array $filters = [])
    {
        $query = $this->variant->newQuery();

        return $query;
    }

    public function createVariant(array $inputs = [])
    {

    }


    public function showVariantById(array $inputs = [])
    {

    }

    public function updateVariant(array $inputs = [])
    {

    }
}
