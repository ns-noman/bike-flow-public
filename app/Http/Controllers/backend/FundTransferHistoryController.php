<?php

namespace App\Http\Controllers\backend;

use App\Models\Account;
use App\Models\FundTransferHistory;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Auth;

class FundTransferHistoryController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Fund Transfers'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.fundtransfers.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = FundTransferHistory::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['from_accounts'] = $this->paymentMethods();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.fundtransfers.create-or-edit',compact('data'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by_id'] = $this->getUserId();
        FundTransferHistory::create($data);
        return redirect()->route('fundtransfers.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        $data['updated_by_id'] = $this->getUserId();
        FundTransferHistory::find($id)->update($data);
        return redirect()->route('fundtransfers.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {

        $select =
        [   
            'from_accounts.account_no as from_account_no',
            'to_accounts.account_no as to_account_no',
            'from_payment_methods.name as from_payment_method',
            'to_payment_methods.name as to_payment_method',
            'fund_transfer_histories.id',
            'fund_transfer_histories.transfer_date',
            'fund_transfer_histories.from_account_id',
            'fund_transfer_histories.to_account_id',
            'fund_transfer_histories.amount',
            'fund_transfer_histories.reference_number',
            'fund_transfer_histories.description',
            'fund_transfer_histories.status',
            'fund_transfer_histories.created_by_id',
            'fund_transfer_histories.updated_by_id',
        ];

        $query = FundTransferHistory::join('accounts as from_accounts', 'from_accounts.id', '=', 'fund_transfer_histories.from_account_id')
                            ->join('accounts as to_accounts', 'to_accounts.id', '=', 'fund_transfer_histories.to_account_id')
                            ->join('payment_methods as from_payment_methods', 'from_payment_methods.id', '=', 'from_accounts.payment_method_id')
                            ->join('payment_methods as to_payment_methods', 'to_payment_methods.id', '=', 'to_accounts.payment_method_id')
                            ;
        if(!$request->has('order')) $query = $query->orderBy('id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }

    public function approve($id)
    {  
        try {
            DB::beginTransaction();
            $fundTransferHistory = FundTransferHistory::find($id);
            $transfer_from_id = $fundTransferHistory->from_account_id;
            $transfer_to_id = $fundTransferHistory->to_account_id;
            $amount = $fundTransferHistory->amount;
            $description = $fundTransferHistory->description;
            $reference_number = $fundTransferHistory->reference_number;
            $date = $fundTransferHistory->transfer_date;

            $debit_account['account_id'] = $transfer_from_id;
            $debit_account['credit_amount'] = null;
            $debit_account['debit_amount'] = $amount;
            $debit_account['reference_number'] = null;
            $debit_account['description'] = 'Transfered to other account';
            $debit_account['transaction_date'] = $date;
            $this->accountTransaction($debit_account);

            $credit_amount['account_id'] = $transfer_to_id;
            $credit_amount['credit_amount'] = $amount;
            $credit_amount['debit_amount'] = null;
            $credit_amount['reference_number'] = $reference_number;
            $credit_amount['description'] = 'Received from other account';
            $credit_amount['transaction_date'] = $date;
            $this->accountTransaction($credit_amount);
            $fundTransferHistory->update(['status'=> 1]);
            DB::commit();
            return response()->json(['success'=>true,'message'=>'Transaction approved successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error Approving!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
