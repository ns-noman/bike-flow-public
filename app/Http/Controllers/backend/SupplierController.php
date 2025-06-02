<?php

namespace App\Http\Controllers\backend;

use App\Models\Supplier;
use App\Models\BasicInfo;
use App\Models\SupplierLedger;
use App\Models\Purchase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class SupplierController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Suppliers'];}
    public function index()
    {
        $data['suppliers'] = Supplier::orderBy('id', 'desc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.suppliers.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Supplier::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.suppliers.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        //Supplier Create**********
        $data = $request->all();
        $data['created_by_id'] = Auth::guard('admin')->user()->id;
        $data['opening_payable'] = $data['opening_payable'] ?? 0;
        $data['opening_receivable'] = $data['opening_receivable'] ?? 0;
        $supplier = Supplier::create($data);
        //End
        //Supplier Ledger Payment Create**********
        if($data['opening_payable'])
        {
            $supplierLedgerData['supplier_id'] = $supplier->id;
            $supplierLedgerData['particular'] = 'Opening Payable';
            $supplierLedgerData['date'] = date('Y-m-d');
            $supplierLedgerData['credit_amount'] = null;
            $supplierLedgerData['debit_amount'] = $data['opening_payable'];
            $supplierLedgerData['created_by_id'] = Auth::guard('admin')->user()->id;
            $this->supplierLedgerTransction($supplierLedgerData);
        }
        if($data['opening_receivable'])
        {
            $supplierLedgerData['supplier_id'] = $supplier->id;
            $supplierLedgerData['particular'] = 'Opening Receivable';
            $supplierLedgerData['date'] = date('Y-m-d');
            $supplierLedgerData['credit_amount'] = $data['opening_receivable'];
            $supplierLedgerData['debit_amount'] = null;
            $supplierLedgerData['created_by_id'] = Auth::guard('admin')->user()->id;
            $this->supplierLedgerTransction($supplierLedgerData);
        }
        //End
        return redirect()->route('suppliers.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }


    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $data = $request->all();
        $data['supplier_by_id'] = Auth::guard('admin')->user()->id;
        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        $data = Purchase::where('supplier_id',$id)->get();
        if(count($data)) return redirect()->back()->with('alert',['messageType'=>'warning','message'=>'Data Deletion Failed!']);
        $supplier->delete();
        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }
}
