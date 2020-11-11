<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class BrandController extends Controller
{
    use UploadAble;

    protected function setPageTitle($title, $subTitle)
    {
        view()->share(['pageTitle' => $title, 'subTitle' => $subTitle]);
    }

    public function index()
    {
        $brands = Brand::all();

        $this->setPageTitle('Brands', 'List of all brands');
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        $this->setPageTitle('Brands', 'Create Brand');
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191',
            'logo' => 'mimes:jpg,jpeg,png|max:1000'
        ]);

        $params = $request->except('_token');

        $collection = collect($params);

        $logo = null;

        if ($collection->has('logo') && ($params['logo'] instanceof UploadedFile)) {
            $logo = $this->uploadOne($params['logo'], 'brands');
        }

        $merge = $collection->merge(compact('logo'));

        $brand = new Brand($merge->all());

        $brand->save();

        if (!$brand) {
            return redirect()->back()->with('error', 'Error occurred while creating brand.');
        }
        return redirect()->route('admin.brands.index')->with('success', 'Brand added successfully');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);

        $this->setPageTitle('Brands', 'Edit Brand : ' . $brand->name);
        return view('admin.brands.edit', compact('brand'));
    }


    public function update(Request $request,$id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => 'required|max:191',
            'logo' => 'mimes:jpg,jpeg,png|max:1000'
        ]);

        $request_data = $request->except('_token','logo');

        if ($request->has('logo') && ($request['logo'] instanceof  UploadedFile)) {

            if ($brand->logo != null) {
                $this->deleteOne($brand->logo);
            }

            $request_data['logo'] = $this->uploadOne($request['logo'], 'brands');
        }

      //  $merge = $collection->merge(compact('logo'));

        $brand->update($request_data);

        if (!$brand) {
            return redirect()->back()->with('error', 'Error occurred while updating brand.');
        }
        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully');
    }

    public function destroy($id)
    {
        //
        $brand = Brand::findOrFail($id);

        if ($brand->logo != null) {
            $this->deleteOne($brand->logo);
        }

        $brand->delete();

        if (!$brand) {
            return redirect()->back( )->with('error', 'Error occurred while deleting brand.');
        }
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully');

    }
}
