<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductFormRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected function setPageTitle($title, $subTitle)
    {
        view()->share(['pageTitle' => $title, 'subTitle' => $subTitle]);
    }

    public function index()
    {
        $products = Product::all();

        $this->setPageTitle('Products', 'Products List');
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        $this->setPageTitle('Products', 'Create Product');
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(StoreProductFormRequest $request)
    {
        $request_data = $request->except('_token');
        $request_data['featured'] = $request->has('featured') ? 1 : 0;
        $request_data['status'] = $request->has('status') ? 1 : 0;

        $product = new Product($request_data);

       // return $product;
        $product->save();

        if ($request->has('categories')) {
            $product->categories()->sync($request_data['categories']);
        }

        if (!$product) {
            return redirect()->back()->with('error', 'Error occurred while creating product.');
        }
        return redirect()->route('admin.products.index')->with('success', 'Product added successfully');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $brands = Brand::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();

        $this->setPageTitle('Products', 'Edit Product');
        return view('admin.products.edit', compact('categories', 'brands', 'product'));
    }

    public function update(StoreProductFormRequest $request,$id)
    {
        $product = Product::findOrFail($id);

        $request_data = $request->except('_token');

        $request_data['featured'] = $request->has('featured') ? 1 : 0;
        $request_data['status'] = $request->has('status') ? 1 : 0;

        $product->update($request_data);

        if ($request->has('categories')) {
            $product->categories()->sync($request_data['categories']);
        }

        if (!$product) {
            return redirect()->back()->with('error', 'Error occurred while updating product.');
        }
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }
}
