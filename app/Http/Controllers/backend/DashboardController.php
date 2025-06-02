<?php

namespace App\Http\Controllers\backend;

use App\Models\BasicInfo;
use App\Models\BikePurchase;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\BikeProfitShareRecords;
use App\Models\InvestorTransaction;
use App\Models\Investor;
use App\Models\Item;
use App\Models\Account;
use App\Models\PartyLoan;
use App\Models\BikeServiceRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class DashboardController extends Controller
{
    protected $breadcrumb;

    public function __construct()
    {
        $this->breadcrumb = ['title' => 'Dashboard'];
    }

    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        $investor_id = Auth::guard('admin')->user()->investor_id;

        $data['basicInfo'] = BasicInfo::first()->toArray();

        $data['bike_stock'] = BikePurchase::where('purchase_status', 1)
                                ->where('selling_status', 0)
                                ->count();
        
        $data['investor_bike'] = BikePurchase::where('purchase_status', 1)
                                ->where('selling_status', 0)
                                ->where('investor_id', '!=', 1)
                                ->count();
        
        $data['my_bike'] = BikePurchase::where('purchase_status', 1)
                                ->where('selling_status', 0)
                                ->where('investor_id', Auth::guard('admin')->user()->investor_id)
                                ->count();
        

        $soldBikeQuery = BikePurchase::where('selling_status', 1);
        if ($investor_id != 1) {
            $soldBikeQuery->where('investor_id', $investor_id);
        }

        $data['total_sold'] = $soldBikeQuery->count();

        $todayssalesQuery = BikePurchase::where('selling_status', 1)
                                ->join('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
                                ->where('bike_sales.sale_date', date('Y-m-d'));
        if ($investor_id != 1) {
            $todayssalesQuery->where('investor_id', $investor_id);
        }
        $data['todayssales'] = $todayssalesQuery->count();
        
       $todaysPurchaseQuery = BikePurchase::where('purchase_status', 1)->where('purchase_date', date('Y-m-d'));
        if ($investor_id != 1) {
            $todaysPurchaseQuery->where('investor_id', $investor_id);
        }
        $data['todayspurchase'] = $todaysPurchaseQuery->count();

        $data['investorProfitPayment'] = BikeProfitShareRecords::where('status', 1)
                                ->whereDate('date', date('Y-m-d'))
                                ->sum('amount');
        $data['newinvestments'] = InvestorTransaction::where(['status'=> 1])
                                ->whereDate('transaction_date', date('Y-m-d'))
                                ->sum('credit_amount');
        $data['investmentwithdrawal'] = InvestorTransaction::where(['transaction_type'=> 0,'status'=> 1])
                                ->whereDate('transaction_date', date('Y-m-d'))
                                ->sum('debit_amount');
        $data['investors_capital'] = Investor::where(['is_self'=>0, 'status'=> 1])
                                    ->sum('investment_capital');
                                    
        $data['total_bike_service_expense'] = BikeServiceRecord::where('status', 1)->sum('total_amount');
                                    
        $data['my_capital'] = Investor::find(Auth::guard('admin')->user()->investor_id)->investment_capital ?? 0;
        $data['my_available_balance'] = Investor::find(Auth::guard('admin')->user()->investor_id)->balance ?? 0;

        $data['totalExpenses_exp'] = Expense::where('status', 1)->sum('total_amount');
        $data['totalPurchase_exp'] = Purchase::where('status', 1)->sum('total_payable');
        $data['totalSale_inc'] = Sale::where('status', 1)->sum('total_payable');
        $data['stockValueItem'] = $this->stockValue();
        $data['stockValueBike'] = $this->bikeStockValue();
        $data['allAccountBalance'] = $this->allAccountBalance();
        $data['totalLoanReceiveable'] = PartyLoan::where(['loan_type'=>0, 'status'=> 1])->where('payment_status','!=',1)->select(DB::raw('SUM(amount-paid_amount) as due'))->value('due');
        $data['totalLoanPayable'] = PartyLoan::where(['loan_type'=>1, 'status'=> 1])->where('payment_status','!=',1)->select(DB::raw('SUM(amount-paid_amount) as due'))->value('due');
        return view('backend.index', compact('data'));
        
    }

    public function summeryData($dateRange)
    {
        $dateRange = explode(' - ',$dateRange);
        $fromDate = explode('_',$dateRange[0]);
        $toDate = explode('_',$dateRange[1]);
        $fromDateFormatted = $fromDate[2] .'-'. $fromDate[0] . '-' . $fromDate[1];
        $toDateFormatted = $toDate[2] .'-'. $toDate[0] . '-' . $toDate[1];

        $data['purchase'] = Purchase::where('status', 1)
            ->whereBetween('date', [$fromDateFormatted, $toDateFormatted])
            ->sum('total_payable');
        $data['expenses'] = Expense::where('status', 1)
            ->whereBetween('date', [$fromDateFormatted, $toDateFormatted])
            ->sum('total_amount');

        $data['accessories'] = Sale::join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
            ->join('items', 'items.id', '=', 'sale_details.item_id')
            ->where(['sales.status'=> 1, 'sale_details.item_type'=> 0])
            ->where('items.cat_type_id', 1)
            ->whereBetween('sales.date', [$fromDateFormatted, $toDateFormatted])
            ->select(DB::raw('SUM(sale_details.quantity * sale_details.net_sale_price) as total'))
            ->value('total');

        $data['spareparts'] = Sale::join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
            ->join('items', 'items.id', '=', 'sale_details.item_id')
            ->where(['sales.status'=> 1, 'sale_details.item_type'=> 0])
            ->where('items.cat_type_id', 2)
            ->whereBetween('sales.date', [$fromDateFormatted, $toDateFormatted])
            ->select(DB::raw('SUM(sale_details.quantity * sale_details.net_sale_price) as total'))
            ->value('total');

        $data['services'] = Sale::join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
            ->where(['sales.status'=> 1, 'sale_details.item_type'=> 1])
            ->whereBetween('sales.date', [$fromDateFormatted, $toDateFormatted])
            ->select(DB::raw('SUM(sale_details.quantity * sale_details.net_sale_price) as total'))
            ->value('total');
        return response()->json($data, 200);
    }
    public function allAccountBalance()
    {
        return Account::where('status',1)->sum('balance');
    }
    public function stockValue()
    {
        return DB::table('items')
            ->where('status', 1)
            ->select(DB::raw('SUM(current_stock * purchase_price) as stockvalue'))
            ->value('stockvalue');
    }
    public function bikeStockValue()
    {
       return BikePurchase::where(['purchase_status'=> 1,'selling_status'=> 0,])
            ->sum('purchase_price');
    }

    
    public function bikeList(Request $request)
    {
        $select = [
            'bike_purchases.id',
            'investors.name as investor_name',
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'sellers.name as seller_name',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bike_purchases.seller_id',
            'bike_purchases.purchase_price',
            'bike_purchases.servicing_cost',
            'bike_purchases.total_cost',
            'bike_purchases.purchase_date',
        ];
    
        // Default selling status: unsold
        $selling_status = 0;
    
        // Get the logged-in investor ID (if available)
        $investor_id = Auth::guard('admin')->user()->investor_id ?? null;
    
        // Base query with joins
        $query = BikePurchase::join('investors', 'investors.id', '=', 'bike_purchases.investor_id')
            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
            ->join('colors', 'colors.id', '=', 'bikes.color_id')
            ->join('accounts', 'accounts.id', '=', 'bike_purchases.account_id')
            ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
            ->join('sellers', 'sellers.id', '=', 'bike_purchases.seller_id')
            ->leftJoin('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id');
    
        // Apply filters
        switch ($request->filteron) {
            case 'bikestock':
                    $query->where(['bike_purchases.selling_status'  => 0]);
                break;
            case 'mybikes':
                if ($investor_id) {
                    $query->where('bike_purchases.investor_id', $investor_id)->where(['bike_purchases.selling_status'  => 0]);
                }
                break;
    
            case 'investorbikes':
                if ($investor_id) {
                    $query->where('bike_purchases.investor_id', '!=', $investor_id)->where(['bike_purchases.selling_status'  => 0]);
                }
                break;
    
            case 'soldbikes':
                if ($investor_id != 1) {
                    $query->where('investor_id', $investor_id);
                }
                $query->where(['bike_purchases.selling_status'  => 1]);
                break;
    
        case 'todayspurchases':
                if ($investor_id != 1) {
                    $query->where('investor_id', $investor_id);
                }
                $query->whereDate('bike_purchases.purchase_date', Carbon::today());
                break;
            case 'todayssales':
                if ($investor_id != 1) {
                    $query->where('investor_id', $investor_id);
                }
                $query->whereDate('bike_sales.sale_date', Carbon::today())->where(['bike_purchases.selling_status'  => 1]);
                break;
        }
    
        // Apply common filters
        $query->where([
            'bike_purchases.purchase_status' => 1,
        ]);
    
        // Default ordering
        if (!$request->has('order')) {
            $query->orderBy('bike_purchases.id', 'desc');
        }
    
        // Select final fields
        $query->select($select);
    
        // Return result as DataTable
        $query2 = clone $query;
        $total = $query2->select(DB::raw('SUM(purchase_price) as total_purchase_price,SUM(servicing_cost) as total_servicing_cost, SUM(total_cost) as grand_total_cost'))->first();
        return DataTables::of($query)->with(['summery_data'=> $total])->make(true);
    }
    

}
