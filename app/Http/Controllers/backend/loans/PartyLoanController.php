<?php

namespace App\Http\Controllers\backend\loans;

use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\BasicInfo;
use App\Models\PaymentMethod;
use App\Models\CustomerPayment;
use App\Models\Party;
use App\Models\PartyLoan;
use App\Models\Item;
use App\Models\BikeService;
use App\Models\StockHistory;
use App\Models\PartyPayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class PartyLoanController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Loans'];}
    public function index()
    {
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['paymentMethods'] = $this->paymentMethods();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.loans.loans.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Sale::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['paymentMethods'] = $this->paymentMethods();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['parties'] = Party::where('status',1)->orderBy('name','asc')->get();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.loans.loans.create-or-edit',compact('data'));
    }

    public function inovice($id, $print=null)
    {
        $data['breadcrumb'] = $this->breadcrumb;
        $data['print'] = $print;

        $select = 
        [
            'party_loans.id',
            'party_loans.party_id',
            'party_loans.account_id',
            'party_loans.loan_no',
            'party_loans.loan_type',
            'party_loans.loan_date',
            'party_loans.due_date',
            'party_loans.last_payment_date',
            'party_loans.amount',
            'party_loans.paid_amount',
            'party_loans.reference_number',
            'party_loans.note',
            'party_loans.payment_status',
            'party_loans.status',
            'party_loans.created_by_id',
            'party_loans.updated_by_id',
            'parties.name as party_name',
            'parties.phone as party_contact_no',
            'parties.email as party_email',
            'parties.address as party_address',
            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
        ];
        $selectDetails = 
        [
            'accounts.account_no',
            'payment_methods.name as payment_method',
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

        $data['basicInfo'] = BasicInfo::first()->toArray();
        $data['master'] = PartyLoan::join('parties','parties.id','=','party_loans.party_id')
                            ->leftJoin('accounts', 'accounts.id', '=', 'party_loans.account_id')
                            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->join('admins', 'admins.id', '=', 'party_loans.created_by_id')
                            ->where('party_loans.id', $id)
                            ->select($select)
                            ->first()
                            ->toArray();
        $data['details'] = PartyPayment::leftJoin('accounts', 'accounts.id', '=', 'party_payments.account_id')
                            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->where(['party_payments.status'=> 1, 'party_payments.loan_id'=> $data['master']['id'], 'party_payments.party_id'=>$data['master']['party_id']])
                            ->select($selectDetails)
                            ->get();
        $data['details'] = $data['details'] ? $data['details']->toArray() : [];
        
        return view('backend.loans.loans.invoice',compact('data'));
    }
    public function payment(Request $request)
    {
        $data = $request->all();
        $data['created_by_id'] = Auth::guard('admin')->user()->id;
        $data['payment_type'] = $data['loan_type'] == 1 ? 0 : 1; // if payment_type == 0 then paid to party else collection from party
        PartyPayment::create($data);
        return redirect()->route('party-payments.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }



    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['loan_no'] = $this->formatNumber(PartyLoan::latest()->limit(1)->max('loan_no') + 1);
            $data['created_by_id'] = Auth::guard('admin')->user()->id;
            PartyLoan::create($data);
            DB::commit();
            return redirect()->route('loans.index')->with('alert', ['messageType' => 'success', 'message' => 'Data Inserted Successfully!']);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->back()->with('alert', ['messageType' => 'error', 'message' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }
    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
          
            DB::commit();
            return redirect()->route('loans.index')->with('alert', ['messageType' => 'success', 'message' => 'Data Inserted Successfully!']);
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return redirect()->back()->with('alert', ['messageType' => 'error', 'message' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $partyLoan = PartyLoan::find($id);

            $partyLedger['party_id'] = $partyLoan->party_id;
            $partyLedger['loan_id'] = $partyLoan->id;
            $partyLedger['account_id'] = $partyLoan->account_id;
            $partyLedger['particular'] = $partyLoan->loan_type == 1 ? 'Loan Taken' : 'Loan Given';
            $partyLedger['date'] = $partyLoan->loan_date;
            $partyLedger['debit_amount'] = $partyLoan->loan_type == 1 ? $partyLoan->amount : null;
            $partyLedger['credit_amount'] = $partyLoan->loan_type == 0 ? $partyLoan->amount : null;
            $partyLedger['reference_number'] = $partyLoan->reference_number;
            $partyLedger['note'] = $partyLoan->note;
            $partyLedger['created_by_id'] = $partyLoan->created_by_id;
            $this->partyLedgerTransction($partyLedger);

            $accountData['debit_amount'] = $partyLoan->loan_type == 0 ? $partyLoan->amount : null;
            $accountData['credit_amount'] = $partyLoan->loan_type == 1 ? $partyLoan->amount : null;
            $accountData['description']= $partyLoan->loan_type == 1 ? 'Loan Taken' : 'Loan Given';
            $accountData['account_id']= $partyLoan->account_id;
            $accountData['reference_number']= $partyLoan->reference_number;
            $accountData['transaction_date']= $partyLoan->loan_date;
            $this->accountTransaction($accountData);


            // Investor Transaction
             $investor_transaction = [
                'investor_id'=> 1,
                'account_id'=> $partyLoan->account_id,
                'credit_amount'=> $partyLoan->loan_type == 1 ? $partyLoan->amount : null,
                'debit_amount'=> $partyLoan->loan_type == 0 ? $partyLoan->amount : null,
                'transaction_date'=> $partyLoan->loan_date,
                'particular'=> $partyLoan->loan_type == 1 ? 'Loan Taken' : 'Loan Given',
                'reference_number'=> $partyLoan->reference_number,
            ];
            $this->investorLedger($investor_transaction);

            $partyLoan->update(['status'=>1]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Loan Approved Successfully!',
                'loan' =>  $partyLoan,
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error approving loan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        Sale::destroy($id);
        SaleDetails::where('sale_id',$id)->delete();
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }

    public function list(Request $request)
    {
        $select = 
        [
            'party_loans.id',
            'parties.name as party_name',
            'parties.phone as party_contact_no',
            'admins.name as creator_name',
            'payment_methods.name as payment_method',
            'party_loans.party_id',
            'party_loans.loan_no',
            'party_loans.loan_type',
            'party_loans.loan_date',
            'party_loans.due_date',
            'party_loans.last_payment_date',
            'party_loans.amount',
            'party_loans.paid_amount',
            'party_loans.reference_number',
            'party_loans.note',
            'party_loans.payment_status',
            'party_loans.status',
            'party_loans.created_by_id',
            'party_loans.updated_by_id',

        ];
        $payableAmount = PartyLoan::where('loan_type', 1)
                            ->where('status', 1)
                            ->select(DB::raw('SUM(amount - paid_amount) as totalPayable'))->value('totalPayable');
        $receivableAmount = PartyLoan::where('loan_type', 0)
                            ->where('status', 1)
                            ->select(DB::raw('SUM(amount - paid_amount) as receivableAmount'))->value('receivableAmount');


        $query = PartyLoan::join('parties', 'parties.id', '=', 'party_loans.party_id')
                            ->join('accounts', 'accounts.id', '=', 'party_loans.account_id')
                            ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->join('admins', 'admins.id', '=', 'party_loans.created_by_id');
        if(!$request->has('order')) $query = $query->orderBy('party_loans.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->with(['payableAmount'=> $payableAmount, 'receivableAmount'=> $receivableAmount, ])->make(true);
    }

    
    
}