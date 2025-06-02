<?php

namespace App\Http\Controllers\backend;

use App\Models\Customer;
use App\Models\BasicInfo;
use App\Models\CustomerLedger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class CustomerController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Customers'];}
    public function index()
    {
        $data['customers'] = Customer::where('customer_type', 0)->orderBy('id', 'desc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.customers.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Customer::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.customers.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        //Customer Create**********
        $data = $request->all();
        $data['created_by_id'] = Auth::guard('admin')->user()->id;
        if (!$data['name']) {
            $data['name'] = 'Walk-in Customer';
        }
        $customer = Customer::create($data);
        //End
        //Customer Ledger Payment Create**********
        if($data['opening_payable'])
        {
            $customerLedgerData['customer_id'] = $customer->id;
            $customerLedgerData['particular'] = 'Opening Payable';
            $customerLedgerData['date'] = date('Y-m-d');
            $customerLedgerData['credit_amount'] = null;
            $customerLedgerData['debit_amount'] = $data['opening_payable'];
            $customerLedgerData['created_by_id'] = Auth::guard('admin')->user()->id;
            $this->customerLedgerTransction($customerLedgerData);
        }
        if($data['opening_receivable'])
        {
            $customerLedgerData['customer_id'] = $customer->id;
            $customerLedgerData['particular'] = 'Opening Receivable';
            $customerLedgerData['date'] = date('Y-m-d');
            $customerLedgerData['credit_amount'] = $data['opening_receivable'];
            $customerLedgerData['debit_amount'] = null;
            $customerLedgerData['created_by_id'] = Auth::guard('admin')->user()->id;
            $this->customerLedgerTransction($customerLedgerData);
        }
        //End
        return redirect()->route('customers.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }


    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        $data = $request->all();
        $data['created_by_id'] = Auth::guard('admin')->user()->id;
        $customer->update($data);
        return redirect()->route('customers.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if(count($data)) return redirect()->back()->with('alert',['messageType'=>'warning','message'=>'Data Deletion Failed!']);
        $customer->delete();
        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }
}
