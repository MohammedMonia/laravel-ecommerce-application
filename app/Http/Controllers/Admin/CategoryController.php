<?php

namespace App\Http\Controllers\Admin;

use TypiCMS\NestableTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CategoryController extends Controller
{
    use UploadAble;
    use NestableTrait;

    protected function setPageTitle($title, $subTitle)
    {
        view()->share(['pageTitle' => $title, 'subTitle' => $subTitle]);
    }

    public function index()
    {
        $categories = Category::all();
        // return $categories;
        $this->setPageTitle('Categories', 'List of all categories');
        return view('admin.categories.index', compact('categories'));
    }


    public function create()
    {
        $categories = Category::treeList();
        $this->setPageTitle('Categories', 'Create Category');
        return view('admin.categories.create', compact('categories'));
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:191',
            'parent_id' => 'required|not_in:0',
            'image' => 'mimes:jpg,jpeg,png|max:1000'
        ]);

        $params = $request->except('_token');
        $collection = collect($params);

        $image = null;
        if ($collection->has('image') && ($params['image'] instanceof  UploadedFile)) {
            $image = $this->uploadOne($params['image'], 'categories');
        }
        $featured = $collection->has('featured') ? 1 : 0;
        $menu = $collection->has('menu') ? 1 : 0;

        $merge = $collection->merge(compact('menu', 'image', 'featured'));

        $category = new Category($merge->all());

        $category->save();
        if (!$category) {
            return redirect()->back( )->with('error', 'Error occurred while creating category.');
        }
        return redirect()->route('admin.categories.index')->with('success', 'Category added successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $targetCategory = Category::findOrFail($id);

        $categories = Category::treeList();
        $this->setPageTitle('Categories', 'Edit Category : '.$targetCategory->name);
        return view('admin.categories.edit', compact('categories','targetCategory'));
    }


    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'name' => 'required|max:191',
            'parent_id' => 'required|not_in:0',
            'image' => 'mimes:jpg,jpeg,png|max:1000'
        ]);

        $request_data = $request->except('_token','image');

        //$image = null;
        if ($request->has('image') && ($request['image'] instanceof  UploadedFile)) {
            if ($category->image != null) {
                $this->deleteOne($category->image);
            }
            $request_data['image'] = $this->uploadOne($request['image'], 'categories');
        }

        $request_data['featured'] = $request->has('featured') ? 1 : 0;
        $request_data['menu'] = $request->has('menu') ? 1 : 0;


        $category->update($request_data);

       // return $category;

        if (!$category) {
            return redirect()->back( )->with('error', 'Error occurred while updating category.');
        }
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');

    }


    public function destroy($id)
    {
        //
        $category = Category::findOrFail($id);

        if ($category->image != null) {
            $this->deleteOne($category->image);
        }

        $category->delete();

        if (!$category) {
            return redirect()->back( )->with('error', 'Error occurred while deleting category.');
        }
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');

    }

}
