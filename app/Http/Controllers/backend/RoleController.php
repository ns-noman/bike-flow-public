<?php

namespace App\Http\Controllers\backend;

use App\Models\Role;
use App\Models\User;
use App\Models\Menu;
use App\Models\Privilege;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class RoleController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Roles'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.roles.index',compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['items'] = Role::find($id);
            $data['items']->menu_ids = Privilege::where('role_id', $id)->get()->pluck(['menu_id'])->toArray();
        }else{
            $data['title'] = 'Create';
        }
        $data['menus'] = Menu::with('children.children.children')->where(['parent_id'=>0, 'status'=>1])->orderBy('srln','asc')->get();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.roles.create-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $data['role'] = $request->role;
        $role = Role::create($data);
        $menu_ids = $request->menu_id;

        if($menu_ids && (!in_array(1,$menu_ids))) $menu_ids[count($menu_ids)] = 1;

        foreach($menu_ids as $key => $menu_id){
            $data2['role_id'] = $role->id;
            $data2['menu_id'] = $menu_id;
            Privilege::create($data2);
        }
        return redirect()->route('roles.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $role = Role::find($id);
        Privilege::where('role_id', $role->id)->delete();
        $data['created_by'] = Auth::guard('admin')->user()->id;
        $data['role'] = $request->role;
        $role->update($data);
        $menu_ids = $request->menu_id;
        if($menu_ids && (!in_array(1,$menu_ids))){
            $menu_ids[count($menu_ids)] = '1';
        }
        foreach ($menu_ids as $key => $menu_id){
            $data2['role_id'] = $role->id;
            $data2['menu_id'] = $menu_id;
            Privilege::create($data2);
        }
        return redirect()->route('roles.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    public function destroy($id)
    {
        $role = Role::find($id);
        $admin = Admin::where('type',$role->id)->get();
        if(!count($admin))
        {
            Privilege::where('role_id', $role->id)->delete();
            $role->delete();
            return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
        }
        return redirect()->back()->with('alert',['messageType'=>'warning','message'=>'Data Deletion Failed!']);
    }
    public function allRoles(Request $request)
    {
        $query = Role::join('admins', 'roles.created_by', '=', 'admins.id')
                        ->where('is_superadmin',0)
                        ->where('is_default',0)
                        ->select('roles.id', 'admins.name', 'roles.role');
            if(!$request->has('order')) $query = $query->orderBy('id','desc');
        return DataTables::of($query)->make(true);
    }

}
