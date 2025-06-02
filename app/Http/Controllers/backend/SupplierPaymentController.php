<?php

namespace App\Http\Controllers\backend;

use App\Models\Purchase;
use App\Models\BasicInfo;
use App\Models\PaymentMethod;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\PurchaseDetails;
use App\Models\SupplierLedger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class SupplierPaymentController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Payments'];}
    public function index()
    {
        $data['payments'] = SupplierPayment::orderBy('id', 'desc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.payments.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
        }else{
            $data['title'] = 'Create';
        }
        $data['paymentMethods'] = $this->paymentMethods();
        $data['suppliers'] = Supplier::orderBy('name','asc')->get();
        $data['purchases'] = Purchase::where('payment_status',0)->orderBy('date', 'asc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.payments.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $purchase_id = $request->purchase_id;
        $date = $request->date;
        $supplier_id = $request->supplier_id;
        $account_id = $request->account_id;
        $amount = $request->amount;
        $paid_in_advanced = $request->paid_in_advanced;
        $paid_amount = $request->paid_amount;
        $pay_it = $request->pay_it;
        $note = $request->note;
        $created_by_id = Auth::guard('admin')->user()->id;

        if(isset($purchase_id)){
            for ($i=0; $i < count($purchase_id); $i++) {
                if($paid_amount[$i]){
                    //SupplierPayment Create**********
                    $payment = new SupplierPayment();
                    $payment->supplier_id = $supplier_id;
                    $payment->account_id = $account_id;
                    $payment->purchase_id = $purchase_id[$i];
                    $payment->date = $date;
                    $payment->amount = $paid_amount[$i];
                    $payment->note = $note;
                    $payment->status = 0;
                    $payment->created_by_id = $created_by_id;
                    $payment->save();
                    //End*****
                }
            }
        }
        if($paid_in_advanced)
        {
            //SupplierPayment Create**********
            $payment = new SupplierPayment();
            $payment->supplier_id = $supplier_id;
            $payment->account_id = $account_id;
            $payment->date = $date;
            $payment->amount = $paid_in_advanced;
            $payment->note = $note;
            $payment->status = 0;
            $payment->created_by_id = $created_by_id;
            $payment->save();
            //End*****
        }
        return redirect()->route('payments.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }
    public function dueVouchars(Request $request)
    {
        $data['purchases'] = Purchase::where(['payment_status'=>0, 'supplier_id'=> $request->supplier_id])->orderBy('date', 'asc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        return response()->json($data, 200);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        PaymentMethod::find($id)->update($data);
        return redirect()->route('payment-methods.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
    public function destroy($id)
    {
        SupplierPayment::destroy($id);
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }
    public function list(Request $request)
    {
        $select = 
        [
            'supplier_payments.id',
            'suppliers.name as supplier_name',
            'payment_methods.name as payment_method',
            'purchases.vouchar_no',
            'purchases.id as purchase_id',
            'admins.name as creator_name',
            'supplier_payments.supplier_id',
            'supplier_payments.account_id',
            // 'supplier_payments.purchase_id',
            'supplier_payments.date',
            'supplier_payments.amount',
            'supplier_payments.reference_number',
            'supplier_payments.note',
            'supplier_payments.status',
        ];

        $query = SupplierPayment::join('suppliers', 'suppliers.id', '=', 'supplier_payments.supplier_id')
                                ->join('admins', 'admins.id', '=', 'supplier_payments.created_by_id')
                                ->join('accounts', 'accounts.id', '=', 'supplier_payments.account_id')
                                ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                                ->leftJoin('purchases', 'purchases.id', '=', 'supplier_payments.purchase_id');
        if(!$request->has('order')) $query = $query->orderBy('supplier_payments.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $supplierPayment = SupplierPayment::findOrFail($id);
            $supplierPayment->update(['status' => 1]);
            if ($supplierPayment->purchase_id) {
                $purchase = Purchase::find($supplierPayment->purchase_id);
                $purchase->paid_amount += $supplierPayment->amount;
                if ($purchase->total_payable == $purchase->paid_amount) {
                    $purchase->payment_status = 1;
                }
                $purchase->save();
            }

            $accountData = [
                'account_id'        => $supplierPayment->account_id,
                'debit_amount'      => $supplierPayment->amount,
                'reference_number'  => $supplierPayment->reference_number,
                'description'       => 'Regular Purchase',
                'transaction_date'  => $supplierPayment->date, 
            ];
            $this->accountTransaction($accountData);

            $supplierLedgerDataPayment['supplier_id'] = $supplierPayment->supplier_id;
            $supplierLedgerDataPayment['payment_id'] = $supplierPayment->purchase_id;
            $supplierLedgerDataPayment['account_id'] = $supplierPayment->account_id;
            $supplierLedgerDataPayment['particular'] = 'Payment';
            $supplierLedgerDataPayment['date'] = $supplierPayment->date;
            $supplierLedgerDataPayment['credit_amount'] = $supplierPayment->amount;
            $supplierLedgerDataPayment['reference_number'] = $supplierPayment->reference_number;
            $supplierLedgerDataPayment['note'] = $supplierPayment->note;
            $supplierLedgerDataPayment['created_by_id'] = $supplierPayment->created_by_id;
            $this->supplierLedgerTransction($supplierLedgerDataPayment);

             // Investor Transaction
             $investor_transaction = [
                'investor_id'       => 1,
                'account_id'        => $supplierPayment->account_id,
                'debit_amount'      => $supplierPayment->amount,
                'transaction_date'  => $supplierPayment->date,
                'particular'       => "Suppler Payment",
                'reference_number'  => $supplierPayment->reference_number,
            ];
            $this->investorLedger($investor_transaction);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase approved successfully.'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error approving purchase.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
