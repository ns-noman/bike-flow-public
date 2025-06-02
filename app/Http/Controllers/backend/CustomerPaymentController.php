<?php

namespace App\Http\Controllers\backend;

use App\Models\Sale;
use App\Models\BasicInfo;
use App\Models\PaymentMethod;
use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SaleDetails;
use App\Models\CustomerLedger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class CustomerPaymentController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Payments'];}
    public function index()
    {
        $data['payments'] = CustomerPayment::orderBy('id', 'desc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.customer-payments.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
        }else{
            $data['title'] = 'Create';
        }
        $data['paymentMethods'] = $this->paymentMethods();
        $data['customers'] = Customer::orderBy('name','asc')->get();
        $data['sales'] = Sale::where('payment_status',0)->orderBy('date', 'asc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.customer-payments.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $sale_id = $request->sale_id;
        $date = $request->date;
        $customer_id = $request->customer_id;
        $account_id = $request->account_id;
        $amount = $request->amount;
        $paid_in_advanced = $request->paid_in_advanced;
        $paid_amount = $request->paid_amount;
        $pay_it = $request->pay_it;
        $note = $request->note;
        $created_by_id = Auth::guard('admin')->user()->id;

        if(isset($sale_id)){
            for ($i=0; $i < count($sale_id); $i++) {
                if($paid_amount[$i]){
                    //CustomerPayment Create**********
                    $payment = new CustomerPayment();
                    $payment->customer_id = $customer_id;
                    $payment->account_id = $account_id;
                    $payment->sale_id = $sale_id[$i];
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
            //CustomerPayment Create**********
            $payment = new CustomerPayment();
            $payment->customer_id = $customer_id;
            $payment->account_id = $account_id;
            $payment->date = $date;
            $payment->amount = $paid_in_advanced;
            $payment->note = $note;
            $payment->status = 0;
            $payment->created_by_id = $created_by_id;
            $payment->save();
            //End*****
        }
        return redirect()->route('customer-payments.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }
    public function dueInvoice(Request $request)
    {
        $data['sales'] = Sale::where(['payment_status'=>0, 'customer_id'=> $request->customer_id])->orderBy('date', 'asc')->get();
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
        CustomerPayment::destroy($id);
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }
    public function list(Request $request)
    {
        $select = 
        [
            'customer_payments.id',
            'customers.name as customer_name',
            'payment_methods.name as payment_method',
            'sales.invoice_no',
            'sales.id as sale_id',
            'admins.name as creator_name',
            'customer_payments.customer_id',
            'customer_payments.account_id',
            'customer_payments.date',
            'customer_payments.amount',
            'customer_payments.reference_number',
            'customer_payments.note',
            'customer_payments.status',
        ];

        $query = CustomerPayment::join('customers', 'customers.id', '=', 'customer_payments.customer_id')
                                ->join('admins', 'admins.id', '=', 'customer_payments.created_by_id')
                                ->join('accounts', 'accounts.id', '=', 'customer_payments.account_id')
                                ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                                ->leftJoin('sales', 'sales.id', '=', 'customer_payments.sale_id');
        if(!$request->has('order')) $query = $query->orderBy('customer_payments.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $customerPayment = CustomerPayment::findOrFail($id);
            $customerPayment->update(['status' => 1]);
            if ($customerPayment->sale_id) {
                $sale = Sale::find($customerPayment->sale_id);
                $sale->paid_amount += $customerPayment->amount;
                if ($sale->total_payable == $sale->paid_amount) {
                    $sale->payment_status = 1;
                }
                $sale->save();
            }

            $accountData = [
                'account_id'        => $customerPayment->account_id,
                'credit_amount'      => $customerPayment->amount,
                'reference_number'  => $customerPayment->reference_number,
                'description'       => 'Sale Collection',
                'transaction_date'  => $customerPayment->date, 
            ];
            $this->accountTransaction($accountData);

            // Investor Transaction
            $investor_transaction = [
                'investor_id'=> 1,
                'account_id'=> $customerPayment->account_id,
                'credit_amount'=> $customerPayment->amount,
                'transaction_date'=> $customerPayment->date, 
                'particular'=> 'Sale Collection',
                'reference_number'=> $customerPayment->reference_number,
            ];
            $this->investorLedger($investor_transaction);

            $customerLedgerDataPayment['customer_id'] = $customerPayment->customer_id;
            $customerLedgerDataPayment['payment_id'] = $customerPayment->sale_id;
            $customerLedgerDataPayment['account_id'] = $customerPayment->account_id;
            $customerLedgerDataPayment['particular'] = 'Payment';
            $customerLedgerDataPayment['date'] = $customerPayment->date;
            $customerLedgerDataPayment['debit_amount'] = $customerPayment->amount;
            $customerLedgerDataPayment['reference_number'] = $customerPayment->reference_number;
            $customerLedgerDataPayment['note'] = $customerPayment->note;
            $customerLedgerDataPayment['created_by_id'] = $customerPayment->created_by_id;
            $this->customerLedgerTransction($customerLedgerDataPayment);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale approved successfully.'
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
