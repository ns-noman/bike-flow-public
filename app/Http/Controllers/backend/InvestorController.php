<?php

namespace App\Http\Controllers\backend;

use App\Models\Investor;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Hash;
use Auth;

class InvestorController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Investors'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.investors.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Investor::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.investors.create-edit',compact('data'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $admin = Admin::where('email',$data['email'])->first();
        if($admin){
            return redirect()->back()->with('alert',['messageType'=>'danger','message'=>'This email is already exists!']);
        }
        $data['created_by_id'] = $this->getUserId();
        $investor = Investor::create($data);

        $admins['investor_id'] = $investor->id;
        $admins['password'] = Hash::make('12345');
        $admins['name'] = $data['name'];
        $admins['type'] = 2;
        $admins['mobile'] = $data['contact'];
        $admins['email'] = $data['email'];
        $admins['status'] = 0;
        Admin::create($admins);

        return redirect()->route('investors.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        $data['updated_by_id'] = $this->getUserId();
        Investor::find($id)->update($data);
        return redirect()->route('investors.index')->with('alert',['messageType'=>'success','message'=>'User Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {
        $totalInvestmentCapital = Investor::where('status', 1)->sum('investment_capital');
        $totalAvailableBalance = Investor::where('status', 1)->sum('balance');

        $query = Investor::query();
        if (!$request->has('order')) {
            $query = $query->orderBy('id', 'desc');
        }

        return DataTables::of($query)
            ->with(['totalInvestmentCapital' => $totalInvestmentCapital, 'totalAvailableBalance'=> $totalAvailableBalance])
            ->make(true);
    }

}
