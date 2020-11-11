<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug){
        $category = Category::with('products')
            ->where('slug', $slug)
            ->where('menu', 1)
            ->first();

       // dd($category);
        return view('site.pages.category', compact('category'));
    }
}
