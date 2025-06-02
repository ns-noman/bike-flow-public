<?php

namespace App\Http\Controllers\backend\loans;

use App\Models\PartyLoan;
use App\Models\BasicInfo;
use App\Models\PaymentMethod;
use App\Models\PartyPayment;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SaleDetails;
use App\Models\CustomerLedger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class PartyPaymentController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Party Payments'];}
    public function index()
    {
        $data['payments'] = PartyPayment::orderBy('id', 'desc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.loans.party-payments.index', compact('data'));
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
        $data['sales'] = PartyLoan::where('payment_status',0)->orderBy('date', 'asc')->get();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.loans.party-payments.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $loan_id = $request->loan_id;
        $date = $request->date;
        $customer_id = $request->customer_id;
        $account_id = $request->account_id;
        $amount = $request->amount;
        $paid_in_advanced = $request->paid_in_advanced;
        $paid_amount = $request->paid_amount;
        $pay_it = $request->pay_it;
        $note = $request->note;
        $created_by_id = Auth::guard('admin')->user()->id;

        if(isset($loan_id)){
            for ($i=0; $i < count($loan_id); $i++) {
                if($paid_amount[$i]){
                    //PartyPayment Create**********
                    $payment = new PartyPayment();
                    $payment->customer_id = $customer_id;
                    $payment->account_id = $account_id;
                    $payment->loan_id = $loan_id[$i];
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
            //PartyPayment Create**********
            $payment = new PartyPayment();
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
        return redirect()->route('payments.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }
    public function dueInvoice(Request $request)
    {
        $data['sales'] = PartyLoan::where(['payment_status'=>0, 'customer_id'=> $request->customer_id])->orderBy('date', 'asc')->get();
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
        PartyPayment::destroy($id);
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }
    public function list(Request $request)
    {
        $select = 
        [
            'parties.name as party_name',
            'payment_methods.name as payment_method',
            'party_loans.loan_no',
            'admins.name as creator_name',
            'party_payments.id',
            'party_payments.party_id',
            'party_payments.account_id',
            'party_payments.loan_id',
            'party_payments.payment_type',
            'party_payments.date',
            'party_payments.amount',
            'party_payments.reference_number',
            'party_payments.note',
            'party_payments.status',
            'party_payments.created_by_id',
            'party_payments.updated_by_id',
        ];

        $query = PartyPayment::join('parties', 'parties.id', '=', 'party_payments.party_id')
                                ->join('admins', 'admins.id', '=', 'party_payments.created_by_id')
                                ->join('accounts', 'accounts.id', '=', 'party_payments.account_id')
                                ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                                ->leftJoin('party_loans', 'party_loans.id', '=', 'party_payments.loan_id');
        if(!$request->has('order')) $query = $query->orderBy('party_payments.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $partyPayment = PartyPayment::findOrFail($id);
            $partyPayment->update(['status' => 1]);
            if ($partyPayment->loan_id) {
                $partyLoan = PartyLoan::find($partyPayment->loan_id);
                $partyLoan->paid_amount += $partyPayment->amount;
                if ($partyLoan->amount == $partyLoan->paid_amount) {
                    $partyLoan->payment_status = 1;
                }else{
                    $partyLoan->payment_status = -1;
                }
                $partyLoan->last_payment_date = $partyPayment->date;
                $partyLoan->save();
            }

            $accountData['debit_amount'] = $partyPayment->payment_type == 0 ? $partyPayment->amount : null;
            $accountData['credit_amount'] = $partyPayment->payment_type == 1 ? $partyPayment->amount : null;
            $accountData['description']= $partyPayment->payment_type == 1 ? 'Collection From Party' : 'Paid To Party';
            $accountData['account_id']= $partyPayment->account_id;
            $accountData['reference_number']= $partyPayment->reference_number;
            $accountData['transaction_date']= $partyPayment->date;
            $this->accountTransaction($accountData);

            // Investor Transaction
            $investor_transaction = [
                'investor_id'=> 1,
                'account_id'=> $partyPayment->account_id,
                'credit_amount'=> $partyPayment->payment_type == 1 ? $partyPayment->amount : null,
                'debit_amount'=> $partyPayment->payment_type == 0 ? $partyPayment->amount : null,
                'transaction_date'=> $partyPayment->date,
                'particular'=> $partyPayment->payment_type == 1 ? 'Collection From Party' : 'Paid To Party',
                'reference_number'=> $partyPayment->reference_number,
            ];
            $this->investorLedger($investor_transaction);

            $partyLoan = PartyLoan::find($id);
            $partyLedger['payment_id'] = $partyPayment->id;
            $partyLedger['party_id'] = $partyPayment->party_id;
            $partyLedger['account_id'] = $partyPayment->account_id;
            $partyLedger['particular'] = $partyPayment->payment_type == 1 ? 'Collection From Party' : 'Paid To Party';
            $partyLedger['date'] = $partyPayment->date;
            $partyLedger['debit_amount'] = $partyPayment->payment_type == 1 ? $partyPayment->amount : null;
            $partyLedger['credit_amount'] = $partyPayment->payment_type == 0 ? $partyPayment->amount : null;
            $partyLedger['reference_number'] = $partyPayment->reference_number;
            $partyLedger['note'] = $partyPayment->note;
            $partyLedger['created_by_id'] = $partyPayment->created_by_id;
            $this->partyLedgerTransction($partyLedger);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PartyLoan approved successfully.',
                'data' => $partyPayment
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
