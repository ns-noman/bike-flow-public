<?php

namespace App\Http\Controllers\backend;

use App\Models\Account;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class AccountController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Accounts'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.accounts.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Account::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        $data['paymentMethods'] = PaymentMethod::where(['status'=>1, 'is_virtual'=> 0])->select('id','name')->get()->toArray();
        return view('backend.accounts.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by_id'] = $this->getUserId();
        Account::create($data);
        return redirect()->route('accounts.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        $data['updated_by_id'] = $this->getUserId();
        Account::find($id)->update($data);
        return redirect()->route('accounts.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
    public function list(Request $request)
    {
        $accountTotal = Account::join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
            ->where('payment_methods.is_virtual', 0)->sum('balance');


        $query = Account::join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id');
        if(!$request->has('order')) $query = $query->orderBy('id','desc');
        $query = $query->where('payment_methods.is_virtual', 0)->select('accounts.*', 'payment_methods.name as payment_method','payment_methods.is_virtual');
        return DataTables::of($query)->with(['totalAccountBalance'=>$accountTotal])->make(true);
    }

}
