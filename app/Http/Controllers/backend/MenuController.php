<?php

namespace App\Http\Controllers\backend;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class MenuController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Backend Menu'];}
    public function index()
    {
        $data['menus'] = Menu::with('childrenforcreatemenu.childrenforcreatemenu.childrenforcreatemenu.childrenforcreatemenu')->where('parent_id',0)->orderBy('srln','asc')->get()->toArray();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.menus.backend.index',compact('data'));
    }
    public function createOrEdit($id=null,$addmenu=null)
    {
        if($addmenu){
            $data['title'] = 'Create';
            $data['addmenu'] = Menu::find($id)->toArray();
        }else if($id){
            $data['title'] = 'Edit';
            $data['item'] = Menu::find($id)->toArray();
        }else{
            $data['title'] = 'Create';
        }
        $data['menus'] = Menu::with('children.children.children.children')->where('parent_id',0)->orderBy('srln','asc')->get()->toArray();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.menus.backend.create-or-edit',compact('data'));
    }
    public function store(Request $request)
    {
        $data = $request->all();

        if(!$data['srln']) $data['srln'] =  Menu::where('parent_id', $data['parent_id'])->max('srln') + 1;
        $data['is_side_menu'] = isset($data['is_side_menu']) ? 1 : 0;
        $data['status'] = isset($data['status']) ? 1 : 0;
        Menu::create($data);
        return redirect()->route('menus.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['is_side_menu'] = isset($data['is_side_menu']) ? 1 : 0;
        $data['status'] = isset($data['status']) ? 1 : 0;
        Menu::find($id)->update($data);
        return redirect()->route('menus.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }
    public function destroy($id)
    {
        $menu = Menu::find($id);
        $menu->destroy($id);
        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }
}
