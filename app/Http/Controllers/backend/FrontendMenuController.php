<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\FrontendMenu;
use App\Http\Controllers\Controller;
use Auth;

class FrontendMenuController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Frontend Menu'];}
    public function index()
    {
        $data['menus'] = FrontendMenu::with('children_index.children_index.children_index.children_index')->where('parent_id',0)->orderBy('srln','asc')->get()->toArray();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.menus.frontend.index',compact('data'));
    }
    public function createOrEdit($id=null,$addmenu=null)
    {
        if($addmenu){
            $data['title'] = 'Create';
            $data['addmenu'] = FrontendMenu::find($id)->toArray();
        }else if($id){
            $data['title'] = 'Edit';
            $data['item'] = FrontendMenu::find($id)->toArray();
        }else{
            $data['title'] = 'Create';
        }
        $data['menus'] = FrontendMenu::with('children_create.children_create.children_create.children_create')->where('parent_id',0)->orderBy('srln','asc')->get()->toArray();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.menus.frontend.create-or-edit',compact('data'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:255|unique:frontend_menus,title',
            'srln'=>'required|integer'
        ],[
            'title.unque'=>'The title has already been taken. Please choose a different one.',
            'srln.required'=>'Serial No filed is required.',
            'srln.integer'=>'Serial No type should be integer.',
        ]);

        $data = $request->all();
        
        $data['is_in_menus'] = $request->input('is_in_menus', 0);
        $data['is_in_pages'] = $request->input('is_in_pages', 0);
        $data['status'] = $request->input('status', 0);
        FrontendMenu::create($data);
        return redirect()->route('frontend-menus.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['is_in_menus'] = $request->input('is_in_menus', 0);
        $data['is_in_pages'] = $request->input('is_in_pages', 0);
        $data['status'] = $request->input('status', 0);
        FrontendMenu::find($id)->update($data);
        return redirect()->route('frontend-menus.index')->with('alert',['messageType'=>'success','message'=>'Data Update Successfully!']);
    }
}
