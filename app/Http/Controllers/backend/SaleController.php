<?php

namespace App\Http\Controllers\backend;

use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\BasicInfo;
use App\Models\PaymentMethod;
use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\Item;
use App\Models\BikeService;
use App\Models\StockHistory;
use App\Models\CustomerLedger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class SaleController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Sales'];}
    public function index()
    {
      
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['paymentMethods'] = $this->paymentMethods();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.sales.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Sale::find($id);
            $data['saleDetails'] = SaleDetails::leftJoin('items','items.id','sale_details.item_id')
                                    ->leftJoin('units','units.id','items.unit_id')
                                    ->leftJoin('bike_services','bike_services.id','sale_details.service_id')
                                    ->where('sale_id',$id)
                                    ->select([
                                        'sale_details.id',
                                        'sale_details.sale_id',
                                        'sale_details.item_type',
                                        'sale_details.item_id',
                                        'items.name as item_name',
                                        'items.purchase_price',
                                        'items.current_stock',
                                        'units.title as unit_name',
                                        'sale_details.service_id',
                                        'bike_services.name as service_name',
                                        'sale_details.quantity',
                                        'sale_details.unit_price',
                                    ])
                                    ->get()
                                    ->toArray();
        }else{
            $data['title'] = 'Create';
        }
        $data['paymentMethods'] = $this->paymentMethods();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['customers'] = Customer::where('status',1)->orderBy('name','asc')->get();

        $items = Item::join('units','units.id','=','items.unit_id')
                            ->where('items.status',1)
                            ->orderBy('name','asc')
                            ->select('items.id','items.name','items.purchase_price','items.sale_price as price','items.current_stock as stock_quantity','units.title as unit_name')
                            ->get()
                            ->toArray();
        foreach ($items as $key => &$item) {
            $item['item_type'] = 'item';
        }
        $services = BikeService::where('status',1)
                                    ->orderBy('name','asc')
                                    ->select('id','name','price')
                                    ->get()
                                    ->toArray();
        foreach ($services as $key => &$service) {
            $service['item_type'] = 'service';
            $service['unit_name'] = 'Service';
            $service['purchase_price'] = null;
            $service['stock_quantity'] = null;
        }
        $data['items'] = [...$items, ...$services];
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.sales.create-or-edit',compact('data'));
    }

    public function inovice($id, $print=null)
    {
        $data['breadcrumb'] = $this->breadcrumb;
        $data['print'] = $print;

        $select = 
        [
            'sales.customer_id',
            'sales.account_id',
            'sales.invoice_no',
            'sales.bike_reg_no',
            'sales.date as sale_date',
            'sales.total_price',
            'sales.discount',
            'sales.vat_tax',
            'sales.total_payable',
            'sales.paid_amount',
            'sales.reference_number',
            'sales.note',
            'sales.payment_status',
            'sales.status',
            'sales.created_by_id',
            'sales.updated_by_id',
            'customers.name as customer_name',
            'customers.phone as customer_contact',
            'customers.email as customer_email',
            'customers.address as customer_address',
            'customers.organization as customer_organization',
            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
        ];
        $selectDetails = 
        [
            'sale_details.id',
            'sale_details.sale_id',
            'sale_details.item_type',
            'sale_details.item_id',
            'items.name as item_name',
            'units.title as unit_name',
            'sale_details.service_id',
            'bike_services.name as service_name',
            'sale_details.quantity',
            'sale_details.unit_price',
        ];

        $data['basicInfo'] = BasicInfo::first()->toArray();
        $data['master'] = Sale::join('customers','customers.id','=','sales.customer_id')
                            ->leftJoin('accounts', 'accounts.id', '=', 'sales.account_id')
                            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->join('admins', 'admins.id', '=', 'sales.created_by_id')
                            ->where('sales.id', $id)
                            ->select($select)
                            ->first()
                            ->toArray();
        $data['details'] = SaleDetails::leftJoin('items','items.id','sale_details.item_id')
                            ->leftJoin('units','units.id','items.unit_id')
                            ->leftJoin('bike_services','bike_services.id','sale_details.service_id')
                            ->where('sale_details.sale_id',$id)
                            ->select($selectDetails)
                            ->get()
                            ->toArray();


        return view('backend.sales.invoice',compact('data'));
    }
    public function payment(Request $request)
    {
        $sale_id = $request->sale_id;
        $date = $request->date;
        $account_id = $request->account_id;
        $amount = $request->amount;
        $note = $request->note;
        $created_by_id = Auth::guard('admin')->user()->id;
        $sale = Sale::find($request->sale_id);
        $customer_id = $sale->customer_id;

        //CustomerPayment Create**********
        $payment = new CustomerPayment();
        $payment->customer_id = $customer_id;
        $payment->account_id = $account_id;
        $payment->sale_id = $sale_id;
        $payment->date = $date;
        $payment->amount = $amount;
        $payment->note = $note;
        $payment->status = 0;
        $payment->created_by_id = $created_by_id;
        $payment->save();
        //End*****

        return redirect()->route('customer-payments.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function manageCustomer($customerData)
    {
        if($customerData['name']){
            unset($customerData['customer_id']);
            $customerData['status'] = 1;
            if($customerData['phone']){
                $customer = Customer::where('phone', $customerData['phone'])->first();
                if($customer){
                    return $customer->id;
                }
            }
            $customer = Customer::create($customerData);
            $customerData['customer_id'] = $customer->id;
        }
        return $customerData['customer_id'];
    }



    public function store(Request $request)
    {
        // DB::beginTransaction();
        try {
            $customerData['name'] = $request->name;
            $customerData['phone'] = $request->phone;
            $customerData['address'] = $request->address;
            $customerData['customer_id'] = $request->customer_id;
            $customer_id = $this->manageCustomer($customerData);

            $bike_reg_no = $request->bike_reg_no;
            $account_id = $request->account_id;
            $date = $request->date;
            $total_pice = $request->total_price;
            $vat_tax = $request->vat_tax ?? 0;
            $discount_method = $request->discount_method;
            $discount_rate = $request->discount_rate ?? 0;
            $discount = $request->discount ?? 0;
            $total_payable = $request->total_payable;
            $paid_amount = $request->paid_amount;
            $note = $request->note;
            $reference_number = $request->reference_number;
    
            $item_id = $request->item_id;
            $item_type = $request->item_type;
            $unit_price = $request->unit_price;
            $quantity = $request->quantity;
    
            $invoice_no = $this->formatNumber(Sale::latest()->limit(1)->max('invoice_no') + 1);
            $created_by_id = Auth::guard('admin')->user()->id;
            // Sale creation
            $sale = new Sale();
            $sale->customer_id = $customer_id;
            $sale->bike_reg_no = $bike_reg_no;
            $sale->account_id = $account_id;
            $sale->invoice_no = $invoice_no;
            $sale->date = $date;
            $sale->total_price = $total_pice;
            $sale->vat_tax = $vat_tax;
            $sale->discount_method = $discount_method;
            $sale->discount_rate = $discount_rate;
            $sale->discount = $discount;
            $sale->total_payable = $total_payable;
            $sale->paid_amount = $paid_amount;
            $sale->reference_number = $reference_number;
            $sale->note = $note;
            $sale->payment_status = ($total_payable == $paid_amount) ? 1 : 0;
            $sale->status = 0;
            $sale->created_by_id = $created_by_id;
            $sale->save();
            for ($i = 0; $i < count($item_id); $i++) {
                $saleDetails = new SaleDetails();
                $saleDetails->sale_id = $sale->id;
                $saleDetails->item_type = $item_type[$i];
                $saleDetails->item_id = null;
                $saleDetails->service_id = null;
                if($item_type[$i]==0){
                    $saleDetails->item_id = $item_id[$i];
                }else{
                    $saleDetails->service_id = $item_id[$i];
                }
                $saleDetails->quantity = $quantity[$i];
                $saleDetails->unit_price = $unit_price[$i];
                $saleDetails->save();
            }
            // DB::commit();
            return redirect()->route('sales.index')->with('alert', ['messageType' => 'success', 'message' => 'Data Inserted Successfully!']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('alert', ['messageType' => 'error', 'message' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }
    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $customer_id = $request->customer_id;
            $account_id = $request->account_id;
            $date = $request->date;
            $total_pice = $request->total_price;
            $vat_tax = $request->vat_tax ?? 0;
            $discount_method = $request->discount_method;
            $discount_rate = $request->discount_rate ?? 0;
            $discount = $request->discount ?? 0;
            $total_payable = $request->total_payable;
            $paid_amount = $request->paid_amount;
            $note = $request->note;
            $reference_number = $request->reference_number;
    
            $item_id = $request->item_id;
            $item_type = $request->item_type;
            $unit_price = $request->unit_price;
            $quantity = $request->quantity;
    
            $updated_by_id = Auth::guard('admin')->user()->id;
            $sale = Sale::find($id);
            $sale->customer_id = $customer_id;
            $sale->account_id = $account_id;
            $sale->date = $date;
            $sale->total_price = $total_pice;
            $sale->vat_tax = $vat_tax;
            $sale->discount_method = $discount_method;
            $sale->discount_rate = $discount_rate;
            $sale->discount = $discount;
            $sale->total_payable = $total_payable;
            $sale->paid_amount = $paid_amount;
            $sale->reference_number = $reference_number;
            $sale->note = $note;
            $sale->payment_status = ($total_payable == $paid_amount) ? 1 : 0;
            $sale->status = 0;
            $sale->updated_by_id = $updated_by_id;
            $sale->save();

            SaleDetails::where('sale_id', $id)->delete();

            for ($i = 0; $i < count($item_id); $i++) {
                $saleDetails = new SaleDetails();
                $saleDetails->sale_id = $sale->id;
                $saleDetails->item_type = $item_type[$i];
                $saleDetails->item_id = null;
                $saleDetails->service_id = null;
                if($item_type[$i]==0){
                    $saleDetails->item_id = $item_id[$i];
                }else{
                    $saleDetails->service_id = $item_id[$i];
                }
                $saleDetails->quantity = $quantity[$i];
                $saleDetails->unit_price = $unit_price[$i];
                $saleDetails->save();
            }
            DB::commit();
            return redirect()->route('sales.index')->with('alert', ['messageType' => 'success', 'message' => 'Data Inserted Successfully!']);
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

            $sale = Sale::findOrFail($id);

            $customer_id = $sale->customer_id;
            $account_id = $sale->account_id;
            $invoice_no = $sale->invoice_no;
            $date = $sale->date;
            $total_price = $sale->total_price;
            $discount = $sale->discount;
            $vat_tax = $sale->vat_tax;
            $total_payable = $sale->total_payable;
            $paid_amount = $sale->paid_amount;
            $reference_number = $sale->reference_number;
            $note = $sale->note;
            $payment_status = $sale->payment_status;
            $status = $sale->status;
            $created_by_id = $sale->created_by_id;
            $updated_by_id = $sale->updated_by_id;

            // Update sale status
            $sale->update(['status' => 1]);

            $saleDetails = SaleDetails::where('sale_id', $id)->get();

            $totalProfit = 0;
            $totalNetproft = 0;
            $totalPurchasePrice = 0;
            $totalSalesPrice = 0;

            foreach ($saleDetails as $key => &$sd) {
                if ($sd->item_type == 0) {
                    $item = Item::findOrFail($sd->item_id);
                    $item->sale_price = $sd->unit_price;
                    $sd->purchase_price = $item->purchase_price;
                    $sd->subtotal_profit = round(($item->sale_price - $item->purchase_price) * $sd->quantity, 2);
                    $totalPurchasePrice += $item->purchase_price * $sd->quantity;
                    $totalSalesPrice += $item->sale_price * $sd->quantity;
                }else{
                    $bike_service = BikeService::findOrFail($sd->service_id);
                    $bike_service->sale_price = $sd->unit_price;
                    $sd->purchase_price = $bike_service->sale_price * 80/100;
                    $sd->subtotal_profit = round(($bike_service->sale_price - $sd->purchase_price) * $sd->quantity, 2);
                    $totalPurchasePrice += $sd->purchase_price * $sd->quantity;
                    $totalSalesPrice += $bike_service->sale_price * $sd->quantity;
                }
            }
            $totalProfit = $totalSalesPrice - $totalPurchasePrice;

               // Sale details and stock updates
            foreach ($saleDetails as $key => $sd) {
                if ($sd->item_type == 0) {
                    // Update item stock
                    $item = Item::findOrFail($sd->item_id);
                    $item->current_stock -= $sd->quantity;
                    $item->sale_price = $sd->unit_price;
                    $item->save();
        
                    // Stock history update
                    $stockHistory = new StockHistory();
                    $stockHistory->item_id = $sd->item_id;
                    $stockHistory->date = $date;
                    $stockHistory->particular = 'Sale';
                    $stockHistory->stock_out_qty = $sd->quantity;
                    $stockHistory->rate = $sd->unit_price;
                    $stockHistory->current_stock = $item->current_stock;
                    $stockHistory->created_by_id = $created_by_id;
                    $stockHistory->save();

                    $profit_percentage_per_item = ($totalProfit != 0) ? ($sd->subtotal_profit / $totalProfit) * 100 : 0;
                    $discount_of_each_item = $discount * ($profit_percentage_per_item/100);
                    $net_sale_price = $item->sale_price - ($discount_of_each_item/$sd->quantity);
                    $net_subtotal_profit = round($sd->subtotal_profit - $discount_of_each_item, 2);
                    SaleDetails::find($sd->id)->update(
                        [
                            'purchase_price'=>$sd->purchase_price,
                            'profit'=>$sd->subtotal_profit,
                            'net_sale_price'=>$net_sale_price,
                            'net_profit'=>$net_subtotal_profit,
                        ]
                    );
                }else{
                    $bike_service = BikeService::findOrFail($sd->service_id);
                    $profit_percentage_per_item = ($totalProfit != 0) ? ($sd->subtotal_profit / $totalProfit) * 100 : 0;
                    $discount_of_each_item = $discount * ($profit_percentage_per_item/100);
                    $net_sale_price = $bike_service->price - ($discount_of_each_item/$sd->quantity);
                    $net_subtotal_profit = round($sd->subtotal_profit - $discount_of_each_item, 2);
                    SaleDetails::find($sd->id)->update(
                        [
                            'purchase_price'=>$sd->purchase_price,
                            'profit'=>$sd->subtotal_profit,
                            'net_sale_price'=>$net_sale_price,
                            'net_profit'=>$net_subtotal_profit,
                        ]
                    );
                }
            }

            // Customer ledger entry for sale
            $customerLedgerDataSale['customer_id'] = $customer_id;
            $customerLedgerDataSale['sale_id'] = $sale->id;
            $customerLedgerDataSale['particular'] = 'Sale';
            $customerLedgerDataSale['date'] = $date;
            $customerLedgerDataSale['credit_amount'] = $total_payable;
            $customerLedgerDataSale['note'] = $note;
            $customerLedgerDataSale['created_by_id'] = $created_by_id;
            $this->customerLedgerTransction($customerLedgerDataSale);

            if ($paid_amount>0)
            {
            // Account Transaction
                $accountData = [
                    'account_id'        => $account_id,
                    'credit_amount'      => $paid_amount,
                    'reference_number'  => $reference_number,
                    'description'       => 'Sale Payment',
                    'transaction_date'  => $date, 
                ];
                $this->accountTransaction($accountData);

                // Investor Transaction
                $investor_transaction = [
                    'investor_id'=> 1,
                    'account_id'=> $account_id,
                    'credit_amount'=> $paid_amount,
                    'transaction_date'=> $date,
                    'particular'=> 'Sale Payment',
                    'reference_number'=> $reference_number,
                ];
                $this->investorLedger($investor_transaction);

                $payment = new CustomerPayment();
                $payment->customer_id = $customer_id;
                $payment->account_id = $account_id;
                $payment->sale_id = $sale->id;
                $payment->date = $date;
                $payment->amount = $paid_amount;
                $payment->reference_number = $reference_number;
                $payment->note = $note;
                $payment->status = 1;
                $payment->created_by_id = $created_by_id;
                $payment->save();

                $customerLedgerDataPayment['customer_id'] = $customer_id;
                $customerLedgerDataPayment['payment_id'] = $payment->id;
                $customerLedgerDataPayment['account_id'] = $account_id;
                $customerLedgerDataPayment['particular'] = 'Payment';
                $customerLedgerDataPayment['date'] = $date;
                $customerLedgerDataPayment['debit_amount'] = $paid_amount;
                $customerLedgerDataPayment['reference_number'] = $reference_number;
                $customerLedgerDataPayment['note'] = $note;
                $customerLedgerDataPayment['created_by_id'] = $created_by_id;
                $this->customerLedgerTransction($customerLedgerDataPayment);

            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale approved successfully.'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error approving sale.',
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
            'sales.id',
            'customers.name as customer_name',
            'customers.phone as customer_contact',
            'admins.name as creator_name',
            'sales.customer_id',
            'sales.bike_reg_no',
            'sales.invoice_no',
            'sales.date',
            'sales.total_price',
            'sales.discount',
            'sales.vat_tax',
            'sales.total_payable',
            'sales.paid_amount',
            'sales.note',
            'sales.payment_status',
            'sales.status',
            'sales.created_by_id',
            'sales.updated_by_id',

        ];

        $totalSale = Sale::join('customers', 'customers.id', '=', 'sales.customer_id')
                                ->join('admins', 'admins.id', '=', 'sales.created_by_id')
                                ->select(DB::raw('SUM(total_payable) as totalSale'))->value('totalSale');

        $query = Sale::join('customers', 'customers.id', '=', 'sales.customer_id')
                            ->join('admins', 'admins.id', '=', 'sales.created_by_id');
        if(!$request->has('order')) $query = $query->orderBy('sales.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->with(['totalSale'=>$totalSale])->make(true);
    }

    
    
}