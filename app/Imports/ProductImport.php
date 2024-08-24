<?php 

namespace App\Imports;

use App\Models\Product;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // $code = strtoupper(substr($row['itemcode'], 0, 2));
        $existingProduct = Product::where('name', $row['name'])->where('code', $row['itemcode'])->first();

        $existingProductName = Product::where('name', $row['name'])->where('code', '!=', $row['itemcode'])->first();

        if($existingProductName) {
            $existingProductName->code = $row['itemcode'];
            $existingProductName->save();
        }

        if ($existingProduct) {
            return null;
        }
        
        $category = Category::firstOrCreate([
            'name' => $row['activity'], 
            'code' => strtolower($row['activity'])
        ]);

        $categoryId = $category->id; 

        $unit = Unit::firstOrCreate(['name' => $row['uom'], 'short_name' => $row['uom']]);

        return new Product([
            'code' => $row['itemcode'],
            'name' => $row['name'],
            'category_id' => $categoryId,
            'subcategory_id' => $row['subactivity'],
            'unit_id' => $unit->id,
            'brand_id' => Brand::first()->id,
            'price' => 0,
            'cost' => 0,
            'type' => 'product'
        ]);
    }
}
