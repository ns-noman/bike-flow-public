<?php

namespace App\Http\Controllers\backend;


use App\Models\Investor;
use App\Models\BikePurchase;
use App\Models\PaymentMethod;
use App\Models\Brand;
use App\Models\BikeModel;
use App\Models\Color;
use App\Models\Buyer;
use App\Models\Bike;
use App\Models\BikeSale;
use App\Models\BikeProfit;
use App\Models\BasicInfo;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\Account;
use App\Models\AccountLedger;
use App\Models\CategoryType;
use App\Models\Item;
use App\Models\StockHistory;
use App\Models\InvestorTransaction;
use App\Models\InvestorLedger;
use App\Models\BikeServiceRecord;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Auth;

class ReportController extends Controller
{
    protected $basicInfo;
    public function __construct()
    {
        $this->basicInfo = BasicInfo::select('id','title', 'address', 'phone','logo','email')->first()->toArray();
    }
    public function monthlyBikeSales(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Monthly Bike Sales Report'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date;
            $orderBy = $request->has('order');
            $query = $this->monthlyBikeSalesQuery($data);
            if (!$request->has('order')) $query = $query->orderBy('bike_sales.id', 'desc');
            return DataTables::of($query)->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['print'] = $print;
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $query = $this->monthlyBikeSalesQuery($data);
                $data['lists'] = $query->get()->toArray();
                return view('backend.reports.monthly-bike-sales.print', compact('data'));
            }else {
                return view('backend.reports.monthly-bike-sales.index', compact('data'));
            }
        }
    }

    public function monthlyBikeSalesQuery($data)
    {
        $select = [
            'bike_sales.id',

            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'bikes.registration_no',
            'bikes.chassis_no',

            'buyers.name as buyer_name',

            'bike_sales.buyer_id',
            'bike_sales.sale_date',
            'bike_sales.sale_price',
            'bike_sales.status',
            'bike_sales.created_by_id',
        ];
        if ($data['date']) [$year, $month] = explode('-', $data['date']);
        $query = BikeSale::join('bike_purchases', 'bike_purchases.id', '=', 'bike_sales.bike_purchase_id')
            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
            ->join('colors', 'colors.id', '=', 'bikes.color_id')
            ->join('buyers', 'buyers.id', '=', 'bike_sales.buyer_id')
            ->where('bike_sales.status', 1);
        if (!empty($year) && !empty($month)) {
            $query = $query->whereYear('bike_sales.sale_date', $year)
                        ->whereMonth('bike_sales.sale_date', $month);
        }
        $query = $query->select($select);
        return $query;
    }
    public function stockReport(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Item Stock Report'];
        $data['cat_type_id'] = $request->cat_type_id;
        $data['cat_id'] = $request->cat_id;
        if ($request->isMethod("POST")) {

            $orderBy = $request->has('order');
            $query = $this->stockReportQuery($data);

            $query2 = clone $query;
            $totalStockValue = $query2->select(DB::raw('SUM(items.current_stock*items.purchase_price) as totalValue'))->value('totalValue');

            if (!$request->has('order')) $query = $query->orderBy('items.name', 'asc');
            return DataTables::of($query)->with('totalStockValue', $totalStockValue)->make(true);


        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['print'] = $print;
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $query = $this->stockReportQuery($data);
                $data['lists'] = $query->orderBy('items.name', 'asc')->get()->toArray();
                return view('backend.reports.stock-reports.print', compact('data'));
            }else {
                $data['category_types'] = CategoryType::get();
                return view('backend.reports.stock-reports.index', compact('data'));
            }
        }
    }

    public function stockReportQuery($data)
    {
        $select = 
        [
            'items.id',
            'items.name',
            'category_types.title as cat_type_name',
            'categories.title as cat_name',
            'subcategories.title as sub_cat_name',
            'units.title as unit_name',
            'items.purchase_price',
            'items.sale_price',
            'items.vat',
            'items.current_stock',
            'items.image',
            'items.status',
        ];

        $query = Item::join('category_types', 'category_types.id', '=', 'items.cat_type_id')
                        ->join('categories', 'categories.id', '=', 'items.cat_id')
                        ->leftJoin('categories as subcategories', 'subcategories.id', '=', 'items.sub_cat_id')
                        ->join('units', 'units.id', '=', 'items.unit_id')
                        ->where('items.status', 1);
        if($data['cat_type_id']) $query = $query->where('items.cat_type_id', $data['cat_type_id']);
        if($data['cat_id']){
            $cat_id = $data['cat_id'];
            $query = $query->where(function($query) use ($cat_id){
                $query->where('items.cat_id', $cat_id)
                        ->orWhere('items.sub_cat_id', $cat_id);
            });
        }
        $query = $query->select($select);
        return $query;
    }

    public function stockHistory(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Item Stock History'];
        $data['item_id'] = $request->item_id;
        $data['date'] = $request->date ?? date('Y-m');
        if ($request->isMethod("POST")) {
            $orderBy = $request->has('order');
            $query = $this->stockHistoryQuery($data);
            $query2 = clone $query;
            $summery = $query2->select(DB::raw('SUM(stock_histories.stock_in_qty) as stock_in_qty, SUM(stock_histories.stock_out_qty) as stock_out_qty'))->first();
            if (!$request->has('order')) $query = $query->orderBy('stock_histories.id', 'asc');
            return DataTables::of($query)->with(['summery'=> $summery])->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['print'] = $print;
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $query = $this->stockHistoryQuery($data);
                $data['lists'] = $query->orderBy('stock_histories.id', 'asc')->get()->toArray();
                return view('backend.reports.stock-histories.print', compact('data'));
            }else {
                $data['items'] = Item::with('unit')->where('status',1)->orderBy('name','asc')->get();
                return view('backend.reports.stock-histories.index', compact('data'));
            }
        }
    }

    public function stockHistoryQuery($data)
    {
        $select = 
        [
            'items.name as item_name',
            'units.title as unit_name',
            'stock_histories.item_id',
            'stock_histories.date',
            'stock_histories.particular',
            'stock_histories.stock_in_qty',
            'stock_histories.stock_out_qty',
            'stock_histories.rate',
            'stock_histories.current_stock',
            'stock_histories.created_by_id',
        ];
        if ($data['date']) [$year, $month] = explode('-', $data['date']);
        $query = StockHistory::join('items', 'items.id', '=', 'stock_histories.item_id')
                        ->join('units', 'units.id', '=', 'items.unit_id')
                        ->where('stock_histories.item_id', $data['item_id']);
        if (!empty($year) && !empty($month)) {
            $query = $query->whereYear('stock_histories.date', $year)
                        ->whereMonth('stock_histories.date', $month);
        }
        $query = $query->select($select);
        return $query;
    }

    public function bikeInventory(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Bike Inventory Report'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date ?? date('Y-m');
            $orderBy = $request->has('order');
            
            $query = $this->bikeInventoryQuery($data);
            $query2 = clone $query;
            $total = $query2->select(DB::raw('SUM(purchase_price) as total_purchase_price,SUM(servicing_cost) as total_servicing_cost, SUM(total_cost) as grand_total_cost'))->first();
            if (!$request->has('order')) $query = $query->orderBy('bike_sales.id', 'desc');
            return DataTables::of($query)->with(['summery_data'=> $total])->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {

                
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;

                if ($data['date']) {
                    [$year, $month] = explode('-', $data['date']);
                }
                $data['soldBikesInDate'] = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                            ->leftJoin('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
                            ->whereYear('bike_purchases.purchase_date', $year)
                            ->whereMonth('bike_purchases.purchase_date', $month)
                            ->where('bike_purchases.selling_status', 1)->count();

                $data['unsoldBikes'] = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                            ->leftJoin('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
                            ->where('bike_purchases.selling_status', 0)->count();

                $latestPurchaseSubqury = BikePurchase::selectRaw('max(id) as id')->groupBy('bike_id');
                $data['totalBikes'] = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                            ->leftJoin('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
                            ->whereIn('bike_purchases.id', $latestPurchaseSubqury)
                            ->count();
                $query = $this->bikeInventoryQuery($data);
                $data['lists'] = $query->get()->toArray();
                return view('backend.reports.bike-inventory.print', compact('data'));
            }else {
                return view('backend.reports.bike-inventory.index', compact('data'));
            }
        }
    }

    public function bikeInventoryQuery($data)
    {
        $select = [
            'bike_purchases.id',
            // 'bike_models.name as model_name',
            'bike_purchases.purchase_price',
            'bike_purchases.servicing_cost',
            'bike_purchases.total_cost',
            'bike_sales.sale_price',
            'bike_purchases.selling_status',

            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'bikes.registration_no',
            'bikes.chassis_no',
        ];
        
        if ($data['date']) {
            [$year, $month] = explode('-', $data['date']);
        }
        // $latestPurchaseSubqury = BikePurchase::selectRaw('max(id) as id')->groupBy('bike_id');
        $query = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
            ->join('colors', 'colors.id', '=', 'bikes.color_id')
            ->leftJoin('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
            ->where('bike_purchases.selling_status',0);
        if (!empty($year) && !empty($month)) {
            $query = $query->whereYear('bike_purchases.purchase_date', $year)
            ->whereMonth('bike_purchases.purchase_date', $month);
        }
        $query = $query->orderBy('bike_purchases.selling_status', 'asc')->select($select);
        return $query;
    }

    public function myBikes(Request $request)
    {
        $investor_id = Auth::guard('admin')->user()->investor_id;
        $data['breadcrumb'] = ['title' => 'My Bikes'];
        $data['selling_status'] = $request->selling_status;
        if ($request->isMethod("POST")) {
            
            // dd($data['selling_status']);
            $orderBy = $request->has('order');
            $query = $this->myBikesQuery($data);
            if (!$request->has('order')) $query = $query->orderBy('bike_sales.id', 'desc');
            return DataTables::of($query)->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $query = $this->myBikesQuery($data);
                $data['lists'] = $query->get()->toArray();
                return view('backend.reports.my-bikes.print', compact('data'));
            }else {
                return view('backend.reports.my-bikes.index', compact('data'));
            }
        }
    }

    public function myBikesQuery($data)
    {

        $investor_id = Auth::guard('admin')->user()->investor_id;
        
        $select = [
            'bike_purchases.id',
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bike_purchases.purchase_price',
            'bike_purchases.servicing_cost',
            'bike_purchases.total_cost',
            'bike_sales.sale_price',
            'bike_purchases.selling_status',
        ];


        $query = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
            ->join('colors', 'colors.id', '=', 'bikes.color_id')
            ->leftJoin('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
            ->where('bike_purchases.selling_status', $data['selling_status'])
            ->where('bike_purchases.investor_id', $investor_id)
            ->orderBy('bike_purchases.selling_status', 'asc')->select($select);
        return $query;
    }

    public function monthlyExpense(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Monthly Expense Report'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date;
            $orderBy = $request->has('order');
            $query = $this->monthlyExpenseQuery($data);

            if (!$request->has('order')){
                $query = $query->orderBy('expenses.date', 'asc')->orderBy('expense_heads.title', 'asc');
            }

            return DataTables::of($query)->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $query = $this->monthlyExpenseQuery($data);
                $data['lists'] = $query->orderBy('expenses.date', 'asc')->orderBy('expense_heads.title', 'asc')->get()->toArray();
                return view('backend.reports.monthly-expenses.print', compact('data'));
            }else {
                return view('backend.reports.monthly-expenses.index', compact('data'));
            }
        }
    }

    public function monthlyExpenseQuery($data)
    {
        $select = [
            'expenses.id',
            'expense_heads.title as expense_name',
            'expenses.date',
            'expense_details.amount',
            'expense_details.note',
        ];
        if ($data['date']) [$year, $month] = explode('-', $data['date']);

        $query = Expense::join('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
            ->join('expense_heads', 'expense_heads.id', '=', 'expense_details.expense_head_id')
            ->where('expenses.status', 1);
        if (!empty($year) && !empty($month)) {
            $query = $query->whereYear('expenses.date', $year)
                    ->whereMonth('expenses.date', $month);
        }
        $query = $query->select($select);
        return $query;
    }

    public function profitLossStatement(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Profit Loss Statement'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date;
            $result = $this->profitLossStatementQuery($data);
            return response()->json($result, 200);
        }else {
            $print = $request->input('print');
            if ($print=='true'){
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $data['lists'] = $this->profitLossStatementQuery($data);
                return view('backend.reports.profit-loss-statement.print', compact('data'));
            }else {
                return view('backend.reports.profit-loss-statement.index', compact('data'));
            }
        }
    }

    public function profitLossStatementQuery($data)
    {
        $expenseSelect = [
            'expense_heads.id',
            'expense_heads.title as expense_name',
            DB::raw('SUM(expense_details.amount) as expense_sub_total'),
        ];
        [$year, $month] = explode('-', $data['date']);
        $result['expenses'] = Expense::join('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
            ->join('expense_heads', 'expense_heads.id', '=', 'expense_details.expense_head_id')
            ->where('expenses.status', 1)
            ->whereYear('expenses.date', $year)
            ->whereMonth('expenses.date', $month)
            ->groupBy('expense_details.expense_head_id')
            ->orderBy('expenses.date', 'asc')
            ->orderBy('expense_heads.title', 'asc')
            ->select($expenseSelect)
            ->get(); 
        $result['total_expenses'] = $result['expenses']->sum('expense_sub_total');
        $result['bike_sales'] = BikePurchase::join('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
                ->whereYear('bike_sales.sale_date', $year)
                ->whereMonth('bike_sales.sale_date', $month)
                ->where(['bike_sales.status'=>1])
                ->selectRaw('SUM(bike_sales.sale_price - bike_purchases.total_cost) as profit')
                ->value('profit');
        $result['regular_sales'] = Sale::join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                ->whereYear('sales.date', $year)
                ->whereMonth('sales.date', $month)
                ->where(['sales.status'=>1,'sale_details.item_type'=>0])
                ->selectRaw('SUM(sale_details.net_profit) as profit')
                ->value('profit');

        $result['service_sales'] = Sale::join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                ->whereYear('sales.date', $year)
                ->whereMonth('sales.date', $month)
                ->where(['sales.status'=>1,'sale_details.item_type'=>1])
                ->selectRaw('SUM(sale_details.unit_price * sale_details.quantity) as profit')
                ->value('profit');
        $result['total_incomes'] = $result['service_sales'] + $result['bike_sales'] + $result['regular_sales'];
        $result['net_profit'] = $result['total_incomes'] - $result['total_expenses'];
        return $result;
    }

    public function accountLedger(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Account Ledger'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date;
            $data['account_id'] = $request->account_id;
            $query = $this->accountLedgerQuery($data);
            $query2 = clone $query;
            $accountLedgerSummery = $query2->select(DB::raw('SUM(credit_amount) as total_deposit, SUM(debit_amount) as total_withdrawal'))->first();
            return DataTables::of($query)->with(['accountLedgerSummery'=> $accountLedgerSummery])->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $data['account_id'] = $request->account_id;
                $query = $this->accountLedgerQuery($data);
                $data['lists'] = $query->get()->toArray();
                $data['account_info'] = PaymentMethod::join('accounts', 'accounts.payment_method_id', '=','payment_methods.id')
                                        ->where(['payment_methods.status'=>1, 'accounts.status'=>1, 'accounts.id'=> $data['account_id']])
                                        ->select([
                                            'accounts.id',
                                            'payment_methods.name',
                                            'accounts.account_no',
                                        ])
                                        ->first()->toArray();
                return view('backend.reports.account-ledger.print', compact('data'));
            }else {
                $data['paymentMethods'] = $this->paymentMethods();
                return view('backend.reports.account-ledger.index', compact('data'));
            }
        }
    }

    public function accountLedgerQuery($data)
    {
        $select = [
            'account_ledgers.id',
            'account_ledgers.account_id',
            'account_ledgers.debit_amount',
            'account_ledgers.credit_amount',
            'account_ledgers.current_balance',
            'account_ledgers.reference_number',
            'account_ledgers.description',
            'account_ledgers.transaction_date',
        ];
        if ($data['date']) [$year, $month] = explode('-', $data['date']);
        $query = AccountLedger::whereYear('account_ledgers.transaction_date', $year)
                    ->whereMonth('account_ledgers.transaction_date', $month)
                    ->where('account_ledgers.account_id', $data['account_id'])
                    ->orderBy('account_ledgers.id')
                    ->select($select);
        return $query;
    }
    public function investment(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Investment Report'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date;
            $data['investor_id'] = $request->investor_id;
            $query = $this->investmentQuery($data);
            return DataTables::of($query)->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $data['investor_id'] = $request->investor_id;
                $query = $this->investmentQuery($data);
                $data['lists'] = $query->get()->toArray();
                $data['investor_info'] = Investor::find($data['investor_id'])->toArray();

                return view('backend.reports.investment-report.print', compact('data'));
            }else {
                $data['investors'] = Investor::where('status',1)->get();
                return view('backend.reports.investment-report.index', compact('data'));
            }
        }
    }

    public function investmentQuery($data)
    {
        
        $select = [
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
        if ($data['date']) [$year, $month] = explode('-', $data['date']);
        $query = InvestorTransaction::join('investors', 'investors.id', '=', 'investor_transactions.investor_id')
        ->join('accounts', 'accounts.id', '=', 'investor_transactions.account_id')
        ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
        ->where('investor_transactions.investor_id', $data['investor_id']);
        if (!empty($year) && !empty($month)) {
            $query = $query->whereYear('investor_transactions.transaction_date', $year)
                    ->whereMonth('investor_transactions.transaction_date', $month);
        }
        $query = $query->select($select);
        return $query;
    }
    public function investorLedgerReport(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Investor Ledger Report'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date;
            $data['investor_id'] = $request->investor_id;
            $query = $this->investorLedgerQuery($data);
            $query2 = clone $query;
            $investorLedgerSummery = $query2->select(DB::raw('SUM(credit_amount) as total_deposit, SUM(debit_amount) as total_withdrawal'))->first();
            return DataTables::of($query)->with(['investorLedgerSummery'=> $investorLedgerSummery])->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $data['investor_id'] = $request->investor_id;
                $query = $this->investorLedgerQuery($data);

                $data['lists'] = $query->get()->toArray();
                $data['investor_info'] = Investor::find($data['investor_id'])->toArray();

                return view('backend.reports.investor-ledger-report.print', compact('data'));
            }else {
                $data['investors'] = Investor::where('status',1)->get();
                return view('backend.reports.investor-ledger-report.index', compact('data'));
            }
        }
    }

    public function investorLedgerQuery($data)
    {
        
        $select = [
            'investor_ledgers.id',
            'investors.name as investor_name',
            'payment_methods.name as payment_method_name',
            'investor_ledgers.particular',
            'investor_ledgers.reference_number',
            // 'investor_ledgers.description',
            'investor_ledgers.transaction_date',
            'investor_ledgers.credit_amount',
            'investor_ledgers.debit_amount',
            'investor_ledgers.current_balance',
            // 'investor_ledgers.status',
        ];
        if ($data['date']) [$year, $month] = explode('-', $data['date']);
        $query = InvestorLedger::join('investors', 'investors.id', '=', 'investor_ledgers.investor_id')
        ->join('accounts', 'accounts.id', '=', 'investor_ledgers.account_id')
        ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
        ->where('investor_ledgers.investor_id', $data['investor_id']);
        if (!empty($year) && !empty($month)) {
            $query = $query->whereYear('investor_ledgers.transaction_date', $year)
                    ->whereMonth('investor_ledgers.transaction_date', $month);
        }
        $query = $query->select($select);
        return $query;
    }



    public function bikeProfit(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Bike Profit Report'];
        if ($request->isMethod("POST")) {
            $data['date'] = $request->date;
            $data['investor_id'] = $request->investor_id;
            $query = $this->bikeProfitQuery($data);
            return DataTables::of($query)->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $data['investor_id'] = $request->investor_id;
                $query = $this->bikeProfitQuery($data);
                $data['lists'] = $query->get()->toArray();
                $data['investor_info'] = Investor::find($data['investor_id'])->toArray();
                return view('backend.reports.bike-profit-reports.print', compact('data'));
            }else {
                $data['investors'] = Investor::where('status',1)->get();
                return view('backend.reports.bike-profit-reports.index', compact('data'));
            }
        }
    }

    public function bikeProfitQuery($data)
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
        if ($data['date']) [$year, $month] = explode('-', $data['date']);

        $query = BikeProfit::with('paymenthistory')->join('bike_sales', 'bike_sales.id', '=', 'bike_profits.bike_sale_id')
            ->join('bike_purchases', 'bike_purchases.id', '=', 'bike_sales.bike_purchase_id')
            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
            ->join('colors', 'colors.id', '=', 'bikes.color_id')
            ->join('admins', 'admins.id', '=', 'bike_profits.created_by_id')
            ->join('investors', 'investors.id', '=', 'bike_profits.investor_id')
            ->where('bike_profits.investor_id', $data['investor_id']);
    
        if (!empty($year) && !empty($month)) {
            $query = $query->whereYear('bike_profits.profit_entry_date', $year)
                ->whereMonth('bike_profits.profit_entry_date', $month);
        }
        $query = $query->select($select);
        return $query;
    }
    public function accountReport(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Accounts Reports'];
        if ($request->isMethod("POST")) {
            $query = $this->accountReportQuery($data);
            $query2 = clone $query;
            $totalAllAccountBalance = $query2->sum('balance');
            if(!$request->has('order')) $query = $query->orderBy('id','desc');
            return DataTables::of($query)->with(['totalAllAccountBalance'=> $totalAllAccountBalance])->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['date'] = $request->date;
                $data['account_id'] = $request->account_id;
                $query = $this->accountReportQuery();
                $data['lists'] = $query->get()->toArray();
                return view('backend.reports.accounts-reports.print', compact('data'));
            }else {
                $data['paymentMethods'] = $this->paymentMethods();
                return view('backend.reports.accounts-reports.index', compact('data'));
            }
        }
    }

    public function accountReportQuery()
    {
        return Account::join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->where(['accounts.status'=>1, 'payment_methods.is_virtual'=>0])
                            ->select('accounts.*', 'payment_methods.name as payment_method');
    }
    public function bikeServiceReport(Request $request)
    {
        $data['breadcrumb'] = ['title' => 'Bike Service Report'];
        $data['bike_purchase_id'] = $request->bike_purchase_id;
        $data['date'] = $request->date ?? date('Y-m');
        if ($request->isMethod("POST")) {
            $orderBy = $request->has('order');
            $query = $this->bikeServiceReportQuery($data);
            $query2 = clone $query;
            $total_service_cost = $query2->select(DB::raw('SUM(bike_service_record_details.quantity * bike_service_record_details.price) as total_service_cost'))->value('total_service_cost');
            if (!$request->has('order')) $query = $query->orderBy('bike_service_records.date', 'asc');
            return DataTables::of($query)->with(['total_service_cost'=> $total_service_cost])->make(true);
        }else {
            $print = $request->input('print');
            if ($print=='true') {

                // $data['breadcrumb'] = $this->breadcrumb;
                // $data['print'] = $print;

                $select = 
                [
                    'bike_sales.id',
                    'bike_models.name as model_name',
                    'colors.name as color_name',
                    'colors.hex_code',
                    'buyers.name as buyer_name',
                    'buyers.contact as buyer_contact',
                    'buyers.nid as buyer_nid',
                    'buyers.dl_no as buyer_dl_no',
                    'buyers.passport_no as buyer_passport_no',
                    'buyers.bcn_no as buyer_bcn_no',
                    'bikes.registration_no',
                    'bikes.chassis_no',
                    'bikes.engine_no',
                    'bike_sales.buyer_id',
                    'bike_sales.sale_date',
                    'bike_sales.sale_price',
                    'bike_sales.note',
                    'bike_sales.reference_number',
                    'bike_sales.status',
                    'bike_purchases.total_cost',
                    'bike_purchases.purchase_price',
                    'bike_purchases.servicing_cost',
                ];

                $data['basicInfo'] = BasicInfo::first()->toArray();
                $data['master'] = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                                        ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                                        ->join('colors', 'colors.id', '=', 'bikes.color_id')
                                        ->leftJoin('bike_sales', 'bike_sales.bike_purchase_id', '=', 'bike_purchases.id')
                                        ->leftJoin('buyers', 'buyers.id', '=', 'bike_sales.buyer_id')
                                        ->where('bike_purchases.id', $data['bike_purchase_id'])
                                        ->select($select)->first()->toArray();
                $data['master']['invoice_no'] = $this->formatNumber($data['master']['id']);



                $data['print'] = $print;
                $data['basicInfo'] = BasicInfo::first()->toArray();
                $query = $this->bikeServiceReportQuery($data);
                $data['lists'] = $query->orderBy('bike_service_records.date', 'asc')->get()->toArray();
                return view('backend.reports.bike-service-report.print', compact('data'));

            }else {
                 $select = [
                    'bike_purchases.id as bike_purchase_id',
                    'bike_purchases.total_cost',
                    'bike_purchases.purchase_date',
                    'bike_models.brand_id',
                    'bike_models.id as model_id',
                    'colors.id as color_id',
                    'colors.hex_code as hex_code',
                    'bike_models.name as model_name',
                    'colors.name as color_name',
                    'bikes.registration_no',
                    'bikes.chassis_no',
                    'bikes.engine_no',
                ];
                $data['bikes'] = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                            ->join('colors', 'colors.id', '=', 'bikes.color_id')
                            ->where(['purchase_status'=>1])
                            ->select($select)->get()->toArray();

                return view('backend.reports.bike-service-report.index', compact('data'));
            }
        }
    }

    public function bikeServiceReportQuery($data)
    {
        $select = 
        [
            'bike_service_record_details.id',
            'bike_service_records.date',
            'bike_service_records.id as bike_service_records_id',
            'bike_services.name as bike_service_name',
            'bike_service_record_details.quantity',
            'bike_service_record_details.price',
            'bike_service_records.invoice_no',
        ];
        $query = BikeServiceRecord::join('bike_service_record_details', 'bike_service_record_details.bike_service_record_id', '=', 'bike_service_records.id')
            ->join('bike_services', 'bike_services.id', '=', 'bike_service_record_details.service_id');
        if ($data['bike_purchase_id']) {
            $query = $query->where('bike_service_records.bike_purchase_id', $data['bike_purchase_id']);
        }
        return $query->select($select);
    }
}
