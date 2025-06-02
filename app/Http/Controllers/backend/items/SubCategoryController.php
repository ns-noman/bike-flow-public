<?php

namespace App\Http\Controllers\backend\items;

use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class SubCategoryController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Sub Category'];}
    public function index()
    {
        $data['sub_categories'] = Category::with(['category_type'])->where('parent_cat_id','!=',0)->orderBy('id', 'desc')->get();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.items.sub-categories.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Category::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['categories'] = Category::where('parent_cat_id', 0)->get();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.items.sub-categories.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if(isset($data['image'])){
            $fileName = 'cat-'. time().'.'. $data['image']->getClientOriginalExtension();
            $data['image']->move(public_path('uploads/category'), $fileName);
            $data['image'] = $fileName;
        }
        $data['cat_type_id'] = Category::find($data['parent_cat_id'])->cat_type_id;
        $category = Category::create($data);
        return redirect()->route('sub-categories.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $data = $request->all();
        if(isset($data['image'])){
            $fileName = 'cat-'. time().'.'. $data['image']->getClientOriginalExtension();
            $data['image']->move(public_path('uploads/category'), $fileName);
            $data['image'] = $fileName;
            if($category->image) unlink(public_path('uploads/category/'.$category->image));
        }
        $category->update($data);
        return redirect()->route('sub-categories.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
    public function destroy($id)
    {
        $category = Category::find($id);
        $data = Item::where('cat_id',$category->id)->get();
        if(count($data)) return redirect()->back()->with('alert',['messageType'=>'warning','message'=>'Data Deletion Failed!']);
        $imagePath = public_path('uploads/category/'.$category->image);
        if($category->image) unlink($imagePath);
        $category->delete();
        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }
}
