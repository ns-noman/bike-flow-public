<?php

namespace App\Http\Controllers\backend;

use App\Models\Investor;
use App\Models\BikeProfit;
use App\Models\InvestorTransaction;
use App\Models\PaymentMethod;

use App\Models\Account;
use App\Models\AccountLedger;
use App\Models\BikeProfitShareRecords;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class BikeProfitController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Bike Profits'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.bike-profits.index', compact('data'));
    }
    public function shareRecords($id)
    {
        $data['breadcrumb'] = ['title'=> 'Bike Profit Share Records'];
        $data['bike_profit_id'] = $id;
        return view('backend.bike-profits.share-records', compact('data'));
    }
    public function createOrEdit($bike_profit_id, $bike_profit_share_records_id = null)
    {
        
        $data['bike_profit'] = BikeProfit::find($bike_profit_id);
        $data['breadcrumb'] = $this->breadcrumb;
        $data['paymentMethods'] = $this->paymentMethods();
        $select = [
            'bike_models.name as model_name',
            'colors.name as color_name',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bikes.engine_no',
        ];
        $data['bike'] = BikeProfit::join('bike_sales', 'bike_sales.id', '=', 'bike_profits.bike_sale_id')
                        ->join('bike_purchases', 'bike_purchases.id', '=', 'bike_sales.bike_purchase_id')
                        ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                        ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                        ->join('colors', 'colors.id', '=', 'bikes.color_id')
                        ->where('bike_profits.id', $bike_profit_id)
                        ->select($select)->first()->toArray();
        if($data['bike_profit']->status) return redirect()->back()->with('alert',['messageType'=>'warning','message'=>'Transaction is closed!']);
        if($bike_profit_share_records_id){
            $data['title'] = 'Edit';
            $data['item'] = BikeProfitShareRecords::find($bike_profit_share_records_id);
        }else{
            $data['title'] = 'Create';
        }

        return view('backend.bike-profits.create-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        BikeProfitShareRecords::create($data);
        return redirect()->route('bike-profits.share-records',$data['bike_profit_id'])->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        BikeProfitShareRecords::find($id)->update($data);
        return redirect()->route('bike-profits.share-records',$data['bike_profit_id'])->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {
        $select = [
            'bike_profits.id',
            'bike_profits.profit_amount',
            'bike_profits.profit_share_amount',
            'bike_profits.profit_entry_date',
            'bike_profits.profit_share_last_date',
            'bike_profits.status',
            'bike_profits.created_by_id',
            
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'bikes.registration_no',
            'bikes.chassis_no',
            
            'admins.name as creator_name',
            'investors.name as investor_name',
        ];
    
        $query = BikeProfit::join('bike_sales', 'bike_sales.id', '=', 'bike_profits.bike_sale_id')
            ->join('bike_purchases', 'bike_purchases.id', '=', 'bike_sales.bike_purchase_id')
            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
            ->join('colors', 'colors.id', '=', 'bikes.color_id')
            ->join('admins', 'admins.id', '=', 'bike_profits.created_by_id')
            ->join('investors', 'investors.id', '=', 'bike_profits.investor_id');
    
        if ($request->has('status')) {
            $query->where('bike_profits.status', $request->status);
        }
        if (!$request->has('order')) {
            $query->orderBy('bike_profits.status')
                  ->orderBy('bike_profits.id', 'desc');
        }
        $query->select($select);
        
        return DataTables::eloquent($query)->make(true);
    }
    public function shareRecordsList(Request $request)
    {
        $select = [
            'bike_profit_share_records.id',
            'bike_profit_share_records.amount',
            'bike_profit_share_records.date',
            'bike_profit_share_records.note',
            'bike_profit_share_records.reference_number',
            'bike_profit_share_records.status',
            
            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
        ];
    
        $query = BikeProfitShareRecords::join('accounts', 'accounts.id', '=', 'bike_profit_share_records.account_id')
            ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
            ->join('admins', 'admins.id', '=', 'bike_profit_share_records.created_by_id')
            ->where('bike_profit_share_records.bike_profit_id', $request->bike_profit_id);
    
        if ($request->has('status')) {
            $query->where('bike_profit_share_records.status', $request->status);
        }
        if (!$request->has('order')) {
            $query->orderBy('bike_profit_share_records.status');
            $query->orderBy('bike_profit_share_records.id', 'desc');
        }
    
    
        $query->select($select);
        
        return DataTables::eloquent($query)->make(true);
    }
    
    public function destroy($id)
    {
        BikeProfitShareRecords::destroy($id);
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }

    public function approve($bp_id, $bpsr_id)
    {
        try {
            DB::beginTransaction();
            
            $transaction = BikeProfitShareRecords::findOrFail($bpsr_id);

            $bike_profit = BikeProfit::findOrFail($bp_id);
            $bike_profit->profit_share_amount = $bike_profit->profit_share_amount + $transaction->amount;
            $bike_profit->profit_share_last_date = $transaction->date;
            $bike_profit->save();

            $accountData = [
                'account_id' => $transaction->account_id,
                'debit_amount' => $transaction->amount,
                'reference_number' => $transaction->reference_number,
                'description' => 'Profit Share Withdrawal',
                'transaction_date' => $transaction->date,
            ];
    
            $this->accountTransaction($accountData);
            $transaction->update(['status' => 1]);

            // Investor Transaction
            $investor_transaction = [
                'investor_id'       => 1,
                'account_id'        => $transaction->account_id,
                'debit_amount'      => $transaction->amount,
                'transaction_date'  => $transaction->date,
                'particular'       => "Bike Profit Payment",
                'reference_number'  => $transaction->reference_number,
            ];
            $this->investorLedger($investor_transaction);
    
            DB::commit();
    
            return response()->json(['success' => true, 'message' => 'Transaction approved successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function changeStatus($id)
    {
        try {
            $transaction = BikeProfit::findOrFail($id);
            $transaction->update(['status' => 1]);
            return response()->json(['success' => true, 'message' => 'Transaction Closed successfully.'], 200);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    

}
