<?php

namespace App\Http\Controllers\backend;

use App\Models\Investor;
use App\Models\InvestorTransaction;
use App\Models\PaymentMethod;
use App\Models\Account;
use App\Models\AccountLedger;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use DB;

use Auth;

class InvestorTransactionController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Investors Transaction'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.investor-transactions.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = InvestorTransaction::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        $data['paymentMethods'] = $this->paymentMethods();
        $data['investors'] = Investor::where('status',1)->get();
        return view('backend.investor-transactions.create-edit',compact('data'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $data['credit_amount'] = null;
        $data['debit_amount'] = null;
        $data[$data['transaction_type']] = $data['amount'];
        InvestorTransaction::create($data);
        return redirect()->route('investor-transactions.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        $data['credit_amount'] = null;
        $data['debit_amount'] = null;
        $data[$data['transaction_type']] = $data['amount'];
        InvestorTransaction::find($id)->update($data);
        return redirect()->route('investor-transactions.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {
        $select = 
        [
            'investor_transactions.id',
            'investors.name as investor_name',
            'payment_methods.name as payment_method_name',
            'investor_transactions.particular',
            'investor_transactions.reference_number',
            'investor_transactions.description',
            'investor_transactions.transaction_date',
            'investor_transactions.credit_amount',
            'investor_transactions.debit_amount',
            'investor_transactions.current_balance',
            'investor_transactions.status',
        ];
        $query = InvestorTransaction::join('investors', 'investors.id', '=', 'investor_transactions.investor_id')
                                    ->join('accounts', 'accounts.id', '=', 'investor_transactions.account_id')
                                    ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id');
                    if(!$request->has('order')) $query = $query->orderBy('investor_transactions.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }
    public function destroy($id)
    {
        InvestorTransaction::destroy($id);
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }

    public function approve($id)
    {
        DB::beginTransaction();

        $transaction = InvestorTransaction::find($id);


        $investor_id = $transaction->investor_id;
        $account_id = $transaction->account_id;
        $credit_amount = $transaction->credit_amount;
        $debit_amount = $transaction->debit_amount;
        $date = $transaction->transaction_date;
        $description = $transaction->description;
        $reference_number = $transaction->reference_number;

        //Investor Transaction Start...
        $currentBalance = InvestorTransaction::where(['status'=>1,'investor_id'=>$investor_id])->latest()->pluck('current_balance')->first() ?? 0;
        $newcurrentBalance = $currentBalance + $credit_amount - $debit_amount;
        $transaction->current_balance = $newcurrentBalance;

        

        $investor = Investor::find($investor_id);
        $investor->investment_capital += ($credit_amount - $debit_amount);
        $investor->save();
        
        $transaction->update(['status' => 1]);
        //Investor Transaction End...

        //Account Transaction Start...
        $accountData['account_id'] = $account_id;
        $accountData['credit_amount'] = $credit_amount;
        $accountData['debit_amount'] = $debit_amount;
        $accountData['reference_number'] = $reference_number;
        $accountData['description'] = $credit_amount ? 'Investment Deposit' : 'Investment Withdrawal';
        $accountData['transaction_date'] = $date;
        $this->accountTransaction($accountData);
        //Account Transaction End...

        // Investor Ledger
        $investorLedger = [
            'investor_id'       => $investor_id,
            'account_id'        => $account_id,
            'debit_amount'      => $debit_amount ?? null,
            'credit_amount'     => $credit_amount ?? null,
            'transaction_date'  => $date, 
            'particular'       => $credit_amount ? 'Investment' : 'Withdrawal' ,
            'reference_number'  => $reference_number,
        ];

        $this->investorLedger($investorLedger);

        

        DB::commit();

        return response()->json(['success'=>true,'message'=>'Transaction approved successfully.'], 200);
    }

}
