<?php


namespace App\Services;


use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * @throws Exception
     */
    public function createProduct(array $inputs = [])
    {
        $productInfo = $this->formatProductInfo($inputs);
        $newProduct = $this->product->newInstance($productInfo);
        DB::beginTransaction();
        try {
            $newProduct->save();
            $inputs['product_id'] = $newProduct->id;

            $productVariantArray = [];

            foreach ($this->formatProductVariantInfo($inputs) as $index => $singleProductVariant):
                $productVariantArray[$index] = new ProductVariant($singleProductVariant);
                $productVariantArray[$index]->save();
            endforeach;

            foreach ($this->formatProductVariantPriceInfo($inputs, $productVariantArray) as $singleProductVariantPrice):
                ProductVariantPrice::create($singleProductVariantPrice);
            endforeach;
            $newProduct->refresh();
            DB::commit();
            return $newProduct;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }

    }

    /**
     * Format Product Inputs
     *
     * @param array $inputs
     * @return array
     */
    private function formatProductInfo(array $inputs = [])
    {
        $product['title'] = $inputs['title'] ?? '';
        $product['sku'] = $inputs['sku'] ?? '';
        $product['description'] = $inputs['description'] ?? '';

        return $product;
    }

    /**
     * Format Product Variant Array Inputs
     *
     * @param array $inputs
     * @return array
     */
    private function formatProductVariantInfo(array $inputs = [])
    {
        $productVariantArray = [];

        foreach ($inputs['product_variant'] as $input):
            $parentArr = [
                'variant_id' => $input['option'],
                'product_id' => $inputs['product_id'],
            ];

            foreach ($input['tags'] as $tag)
                $productVariantArray[] = array_merge($parentArr, [
                    'variant' => $tag
                ]);

        endforeach;

        return $productVariantArray;
    }

    /**
     * Format Product Variant Array Inputs
     *
     * @param array $inputs
     * @param array $productVariantArray
     * @return array
     */
    private function formatProductVariantPriceInfo(array $inputs, array &$productVariantArray)
    {
        $productVariantPriceArray = [];

        $variantFreqArr = [];

        foreach ($productVariantArray as $item):
            $variantFreqArr[$item->variant] = $item->id;
        endforeach;

        foreach ($inputs['product_variant_prices'] as $input):

            $parentArr['price'] = $input['price'] ?? 0;
            $parentArr['stock'] = $input['stock'] ?? 0;
            $parentArr['product_id'] = $inputs['product_id'] ?? 0;

            $titleArray = explode("/", trim($input['title'], "/"));

            if (isset($titleArray[0])) {
                $parentArr['product_variant_one'] = $variantFreqArr[$titleArray[0]] ?? null;
            }

            if (isset($titleArray[1])) {
                $parentArr['product_variant_two'] = $variantFreqArr[$titleArray[1]] ?? null;
            }

            if (isset($titleArray[2])) {
                $parentArr['product_variant_three'] = $variantFreqArr[$titleArray[2]] ?? null;
            }

            $productVariantPriceArray[] = $parentArr;
        endforeach;

        Log::info("Data", $productVariantPriceArray);
        return $productVariantPriceArray;
    }

    /**
     * @throws Exception
     */
    public function showProductById($id)
    {
        try {
            return $this->product::with([
                'productVariants',
                'productImages',
                'productVariantPrices',
                'productVariantPrices.variantOne',
                'productVariantPrices.variantTwo',
                'productVariantPrices.variantThree'
            ])->findOrFail($id);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @param array $inputs
     * @return mixed
     * @throws Exception
     */
    public function updateProduct($id, array $inputs = [])
    {
        $productInfo = $this->formatProductInfo($inputs);
        $existingProduct = $this->showProductById($id);
        $existingProduct->fill($productInfo);
        DB::beginTransaction();
        try {
            $existingProduct->save();
            //
            $inputs['product_id'] = $existingProduct->id;

            $this->removeOldVariants($existingProduct);

            $productVariantArray = [];

            foreach ($this->formatProductVariantInfo($inputs) as $index => $singleProductVariant):
                $productVariantArray[$index] = new ProductVariant($singleProductVariant);
                $productVariantArray[$index]->save();
            endforeach;

            foreach ($this->formatProductVariantPriceInfo($inputs, $productVariantArray) as $singleProductVariantPrice):
                ProductVariantPrice::create($singleProductVariantPrice);
            endforeach;
            $existingProduct->refresh();
            DB::commit();
            return $existingProduct;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    private function removeOldVariants($product)
    {
        foreach ($product->productVariantPrices as $temp) :
            $temp->delete();
        endforeach;

        foreach ($product->productVariants as $temp) :
            $temp->delete();
        endforeach;
    }
}
