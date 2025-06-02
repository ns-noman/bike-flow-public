<?php

namespace App\Http\Controllers\backend;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class ColorController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Colors'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.colors.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Color::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.colors.create-or-edit',compact('data'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        Color::create($data);
        return redirect()->route('colors.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        Color::find($id)->update($data);
        return redirect()->route('colors.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {
        $query = Color::query();
        if(!$request->has('order')) $query = $query->orderBy('id','desc');
        return DataTables::of($query)->make(true);
    }

}
