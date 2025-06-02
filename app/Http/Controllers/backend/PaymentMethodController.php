<?php

namespace App\Http\Controllers\backend;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class PaymentMethodController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Payment Methods'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.payment-methods.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = PaymentMethod::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.payment-methods.create-or-edit',compact('data'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        PaymentMethod::create($data);
        return redirect()->route('payment-methods.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        PaymentMethod::find($id)->update($data);
        return redirect()->route('payment-methods.index')->with('alert',['messageType'=>'success','message'=>'User Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {
        $query = PaymentMethod::where('is_virtual', 0);
        if(!$request->has('order')) $query = $query->orderBy('id','desc');
        return DataTables::of($query)->make(true);
    }

}
