<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    //
    protected function setPageTitle($title, $subTitle)
    {
        view()->share(['pageTitle' => $title, 'subTitle' => $subTitle]);
    }

    public function index()
    {
        $attributes = Attribute::all();

        $this->setPageTitle('Attributes', 'List of all attributes');
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        $this->setPageTitle('Attributes', 'Create Attribute');
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'code'          =>  'required',
            'name'          =>  'required',
            'frontend_type' =>  'required'
        ]);

        $params = $request->except('_token');

        $collection = collect($params);

        $is_filterable = $collection->has('is_filterable') ? 1 : 0;
        $is_required = $collection->has('is_required') ? 1 : 0;

        $merge = $collection->merge(compact('is_filterable', 'is_required'));

        $attribute = new Attribute($merge->all());

        $attribute->save();

        if (!$attribute) {
            return redirect()->back( )->with('error', 'Error occurred while creating attribute.');
        }
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute added successfully');
    }

    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);

        $this->setPageTitle('Attributes', 'Edit Attribute : '.$attribute->name);
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request,$id)
    {
        $attribute = Attribute::findOrFail($id);

        $request->validate([
            'code'          =>  'required',
            'name'          =>  'required',
            'frontend_type' =>  'required'
        ]);

        $params = $request->except('_token');

        $collection = collect($params)->except('_token');

        $is_filterable = $collection->has('is_filterable') ? 1 : 0;
        $is_required = $collection->has('is_required') ? 1 : 0;

        $merge = $collection->merge(compact('is_filterable', 'is_required'));

        $attribute->update($merge->all());;

        if (!$attribute) {
            return redirect()->back( )->with('error', 'Error occurred while updating attribute.');
        }
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully');
    }

    public function destroy($id){
        $attribute = Attribute::findOrFail($id);

        $attribute->delete();

        if (!$attribute) {
            return redirect()->back( )->with('error', 'Error occurred while deleting attribute.');
        }
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully');

    }
}
