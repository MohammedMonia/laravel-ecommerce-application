<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function loadAttributes()
    {
        $attributes = Attribute::all();

        return response()->json($attributes);
    }

    public function productAttributes(Request $request)
    {
        $product = Product::findOrFail($request->id);

        return response()->json($product->attributes);
    }

    public function loadValues(Request $request)
    {
        $attribute = Attribute::findOrFail($request->id);

        return response()->json($attribute->values);
    }

    public function addAttribute(Request $request)
    {
       // dd($request);
       // return $request;
     //   dd($request->all());
/*        [
            'attribute_id' => 1,
            'value' => 1,
            'price' => 1,
            'quantity' => 1,
            'product_id' => 1,
        ]*/
        $data = $request->all();

        $productAttribute = ProductAttribute::create($request->all());


        if ($productAttribute) {
            return response()->json(['message' => 'Product attribute added successfully.']);
        } else {
            return response()->json(['message' => 'Something went wrong while submitting product attribute.']);
        }
    }

    public function deleteAttribute(Request $request)
    {
        $productAttribute = ProductAttribute::findOrFail($request->id);
        $productAttribute->delete();

        return response()->json(['status' => 'success', 'message' => 'Product attribute deleted successfully.']);
    }
}