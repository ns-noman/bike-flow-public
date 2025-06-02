<?php

namespace App\Http\Controllers\backend;

use App\Models\Purchase;
use App\Models\PurchaseRequisition;
use App\Models\BasicInfo;
use App\Models\PaymentMethod;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\PurchaseDetails;
use App\Models\StockHistory;
use App\Models\SupplierLedger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class PurchaseController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Purchases'];}
    public function index()
    {
      
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['paymentMethods'] = $this->paymentMethods();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.purchases.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Purchase::find($id);
            $data['purchaseDetails'] = PurchaseDetails::leftJoin('items','items.id','purchase_details.item_id')
                                    ->leftJoin('units','units.id','items.unit_id')
                                    ->where('purchase_id',$id)
                                    ->select([
                                        'purchase_details.id',
                                        'purchase_details.purchase_id',
                                        'purchase_details.item_id',
                                        'items.name as item_name',
                                        'units.title as unit_name',
                                        'purchase_details.quantity',
                                        'purchase_details.unit_price',
                                    ])
                                    ->get()
                                    ->toArray();
        }else{
            $data['title'] = 'Create';
        }
        $data['paymentMethods'] = $this->paymentMethods();
        $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
        $data['suppliers'] = Supplier::where('status',1)->orderBy('name','asc')->get();
        $data['items'] = Item::with('unit')->where('status',1)->orderBy('name','asc')->get();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.purchases.create-or-edit',compact('data'));
    }

    public function vouchar($id, $print=null)
    {
        $data['breadcrumb'] = $this->breadcrumb;
        $data['print'] = $print;

        $select = 
        [
            'purchases.supplier_id',
            'purchases.account_id',
            'purchases.vouchar_no',
            'purchases.date as purchase_date',
            'purchases.total_price',
            'purchases.discount',
            'purchases.vat_tax',
            'purchases.total_payable',
            'purchases.paid_amount',
            'purchases.reference_number',
            'purchases.note',
            'purchases.payment_status',
            'purchases.status',
            'purchases.created_by_id',
            'purchases.updated_by_id',
            'suppliers.name as supplier_name',
            'suppliers.phone as supplier_contact',
            'suppliers.email as supplier_email',
            'suppliers.address as supplier_address',
            'suppliers.organization as supplier_organization',
            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
        ];
        $selectDetails = 
        [
            'purchase_details.purchase_id',
            'purchase_details.item_id',
            'purchase_details.quantity',
            'purchase_details.unit_price',

            'items.name as item_name',
            'units.title as unit_name',
        ];

        $data['basicInfo'] = BasicInfo::first()->toArray();
        $data['master'] = Purchase::join('suppliers','suppliers.id','=','purchases.supplier_id')
                            ->leftJoin('accounts', 'accounts.id', '=', 'purchases.account_id')
                            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->join('admins', 'admins.id', '=', 'purchases.created_by_id')
                            ->where('purchases.id', $id)
                            ->select($select)
                            ->first()
                            ->toArray();
                            
        $data['details'] = PurchaseDetails::join('items','items.id','=','purchase_details.item_id')
                            ->join('units','units.id','=','items.unit_id')
                            ->where('purchase_details.purchase_id', $id)
                            ->select($selectDetails)
                            ->get()
                            ->toArray();
        return view('backend.purchases.invoice',compact('data'));
    }
    public function payment(Request $request)
    {
        $purchase_id = $request->purchase_id;
        $date = $request->date;
        $account_id = $request->account_id;
        $amount = $request->amount;
        $note = $request->note;
        $created_by_id = Auth::guard('admin')->user()->id;
        $purchase = Purchase::find($request->purchase_id);
        $supplier_id = $purchase->supplier_id;

        //SupplierPayment Create**********
        $payment = new SupplierPayment();
        $payment->supplier_id = $supplier_id;
        $payment->account_id = $account_id;
        $payment->purchase_id = $purchase_id;
        $payment->date = $date;
        $payment->amount = $amount;
        $payment->note = $note;
        $payment->status = 0;
        $payment->created_by_id = $created_by_id;
        $payment->save();
        //End*****

        return redirect()->route('payments.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }



    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->all());
            $supplier_id = $request->supplier_id;
            $account_id = $request->account_id;
            $date = $request->date;
            $total_price = $request->total_price;
            $tax_amount = $request->tax_amount;
            $discount = $request->discount;
            $total_payable = $request->total_payable;
            $paid_amount = $request->paid_amount;
            $note = $request->note;
            $reference_number = $request->reference_number;
    
            $item_id = $request->item_id;
            $unit_price = $request->unit_price;
            $quantity = $request->quantity;
    
            $vouchar_no = $this->formatNumber(Purchase::latest()->limit(1)->max('vouchar_no') + 1);
            $created_by_id = Auth::guard('admin')->user()->id;
            // Purchase creation
            $purchase = new Purchase();
            $purchase->supplier_id = $supplier_id;
            $purchase->account_id = $account_id;
            $purchase->vouchar_no = $vouchar_no;
            $purchase->date = $date;
            $purchase->total_price = $total_price;
            $purchase->vat_tax = $total_price;
            $purchase->discount = $discount;
            $purchase->total_payable = $total_payable;
            $purchase->paid_amount = $paid_amount;
            $purchase->reference_number = $reference_number;
            $purchase->note = $note;
            $purchase->payment_status = ($total_payable == $paid_amount) ? 1 : 0;
            $purchase->status = 0;
            $purchase->created_by_id = $created_by_id;
            $purchase->save();
            for ($i = 0; $i < count($item_id); $i++) {
                $item = Item::findOrFail($item_id[$i]);
                $purchaseDetails = new PurchaseDetails();
                $purchaseDetails->purchase_id = $purchase->id;
                $purchaseDetails->item_id = $item_id[$i];
                $purchaseDetails->quantity = $quantity[$i];
                $purchaseDetails->unit_price = $unit_price[$i];
                $purchaseDetails->save();
            }
            DB::commit();
            return redirect()->route('purchases.index')->with('alert', ['messageType' => 'success', 'message' => 'Data Inserted Successfully!']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('alert', ['messageType' => 'error', 'message' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $supplier_id = $request->supplier_id;
            $account_id = $request->account_id;
            $date = $request->date;
            $total_price = $request->total_price;
            $tax_amount = $request->tax_amount;
            $discount = $request->discount;
            $total_payable = $request->total_payable;
            $paid_amount = $request->paid_amount;
            $note = $request->note;
            $reference_number = $request->reference_number;
    
            $item_id = $request->item_id;
            $unit_price = $request->unit_price;
            $quantity = $request->quantity;

            // Purchase creation
            $purchase = Purchase::find($id);
            $purchase->supplier_id = $supplier_id;
            $purchase->account_id = $account_id;
            $purchase->date = $date;
            $purchase->total_price = $total_price;
            $purchase->vat_tax = $total_price;
            $purchase->discount = $discount;
            $purchase->total_payable = $total_payable;
            $purchase->paid_amount = $paid_amount;
            $purchase->reference_number = $reference_number;
            $purchase->note = $note;
            $purchase->payment_status = ($total_payable == $paid_amount) ? 1 : 0;
            $purchase->status = 0;
            $purchase->save();
            
            PurchaseDetails::where('purchase_id', $id)->delete();

            for ($i = 0; $i < count($item_id); $i++) {
                $item = Item::findOrFail($item_id[$i]);
                $purchaseDetails = new PurchaseDetails();
                $purchaseDetails->purchase_id = $purchase->id;
                $purchaseDetails->item_id = $item_id[$i];
                $purchaseDetails->quantity = $quantity[$i];
                $purchaseDetails->unit_price = $unit_price[$i];
                $purchaseDetails->save();
            }
            DB::commit();
            return redirect()->route('purchases.index')->with('alert', ['messageType' => 'success', 'message' => 'Data Inserted Successfully!']);
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e);
            return redirect()->back()->with('alert', ['messageType' => 'error', 'message' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $purchase = Purchase::findOrFail($id);

            $supplier_id = $purchase->supplier_id;
            $account_id = $purchase->account_id;
            $vouchar_no = $purchase->vouchar_no;
            $date = $purchase->date;
            $total_price = $purchase->total_price;
            $discount = $purchase->discount;
            $vat_tax = $purchase->vat_tax;
            $total_payable = $purchase->total_payable;
            $paid_amount = $purchase->paid_amount;
            $reference_number = $purchase->reference_number;
            $note = $purchase->note;
            $payment_status = $purchase->payment_status;
            $status = $purchase->status;
            $created_by_id = $purchase->created_by_id;
            $updated_by_id = $purchase->updated_by_id;

            // Update purchase status
            $purchase->update(['status' => 1]);

            $purchaseDetails = PurchaseDetails::where('purchase_id', $id)->get();

               // Purchase details and stock updates
            foreach ($purchaseDetails as $key => $pd) {

                // Update item stock
                $item = Item::findOrFail($pd->item_id);
                $item->current_stock += $pd->quantity;
                $item->purchase_price = $pd->unit_price;
                $item->save();
    
                // Stock history update
                $stockHistory = new StockHistory();
                $stockHistory->item_id = $pd->item_id;
                $stockHistory->date = $date;
                $stockHistory->particular = 'Purchase';
                $stockHistory->stock_in_qty = $pd->quantity;
                $stockHistory->rate = $pd->unit_price;
                $stockHistory->current_stock = $item->current_stock;
                $stockHistory->created_by_id = $created_by_id;
                $stockHistory->save();
            }
            // Supplier ledger entry for purchase
            $supplierLedgerDataPurchase['supplier_id'] = $supplier_id;
            $supplierLedgerDataPurchase['purchase_id'] = $purchase->id;
            $supplierLedgerDataPurchase['particular'] = 'Purchase';
            $supplierLedgerDataPurchase['date'] = $date;
            $supplierLedgerDataPurchase['debit_amount'] = $total_payable;
            $supplierLedgerDataPurchase['note'] = $note;
            $supplierLedgerDataPurchase['created_by_id'] = $created_by_id;
            $this->supplierLedgerTransction($supplierLedgerDataPurchase);

            if ($paid_amount>0)
            {
            // Account Transaction
                $accountData = [
                    'account_id'        => $account_id,
                    'debit_amount'      => $paid_amount,
                    'reference_number'  => $reference_number,
                    'description'       => 'Regular Purchase',
                    'transaction_date'  => $date, 
                ];
                $this->accountTransaction($accountData);

                $payment = new SupplierPayment();
                $payment->supplier_id = $supplier_id;
                $payment->account_id = $account_id;
                $payment->purchase_id = $purchase->id;
                $payment->date = $date;
                $payment->amount = $paid_amount;
                $payment->reference_number = $reference_number;
                $payment->note = $note;
                $payment->status = 1;
                $payment->created_by_id = $created_by_id;
                $payment->save();

                $supplierLedgerDataPayment['supplier_id'] = $supplier_id;
                $supplierLedgerDataPayment['payment_id'] = $payment->id;
                $supplierLedgerDataPayment['account_id'] = $account_id;
                $supplierLedgerDataPayment['particular'] = 'Payment';
                $supplierLedgerDataPayment['date'] = $date;
                $supplierLedgerDataPayment['credit_amount'] = $paid_amount;
                $supplierLedgerDataPayment['reference_number'] = $reference_number;
                $supplierLedgerDataPayment['note'] = $note;
                $supplierLedgerDataPayment['created_by_id'] = $created_by_id;
                $this->supplierLedgerTransction($supplierLedgerDataPayment);

                // Investor Transaction
                $investor_transaction = [
                    'investor_id'       => 1,
                    'account_id'        => $account_id,
                    'debit_amount'      => $paid_amount,
                    'transaction_date'  => $date,
                    'particular'       => "Regular Purchase Payment",
                    'reference_number'  => $reference_number,
                ];
                $this->investorLedger($investor_transaction);
            }

      
            
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
    
    public function destroy(Request $request)
    {
        $payment_id = $request->payment_id;
        $purchase_id = $request->purchase_id;
        $payment = SupplierPayment::find($payment_id);
        $supplier_id = $payment->supplier_id;
        
        //Vouchar Update****
        $purchase = Purchase::find($purchase_id);
        $purchase->paid_amount = $purchase->paid_amount - $payment->amount;
        $purchase->payment_status = ($purchase->total_payable <= $purchase->paid_amount)? 1 : 0;
        $purchase->save();
       //End*****

        //Supplier Balance Update****
        $supplier = Supplier::find($supplier_id);
        $supplier->current_balance = $supplier->current_balance + $payment->amount;
        $supplier->save();
        //End*****

        //Supplier Ledger SupplierPayment Destroy**********
        SupplierLedger::where('payment_id', $payment_id)->delete();
        //End*****

        //SupplierPayment Destroy**********
        $payment->delete();
        //End*****

        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }
    public function purchaseRequisition($vouchar_no)
    {
        $data = PurchaseRequisition::with(['prdetails.item.unit'])->where('vouchar_no','=',$this->formatNumber($vouchar_no))->first();
        if($data){
            $data->toArray();
        }
        return response()->json($data, 200);
    }
    public function list(Request $request)
    {
        $select = 
        [
            'purchases.id',
            'suppliers.name as supplier_name',
            'suppliers.phone as supplier_contact',
            'admins.name as creator_name',
            'purchases.supplier_id',
            'purchases.vouchar_no',
            'purchases.date',
            'purchases.total_price',
            'purchases.discount',
            'purchases.vat_tax',
            'purchases.total_payable',
            'purchases.paid_amount',
            'purchases.note',
            'purchases.payment_status',
            'purchases.status',
            'purchases.created_by_id',
            'purchases.updated_by_id',

        ];

        $totalPurchase = Purchase::join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                                ->join('admins', 'admins.id', '=', 'purchases.created_by_id')
                                ->select(DB::raw('SUM(total_payable) as totalPurchase'))->value('totalPurchase');

        $query = Purchase::join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                            ->join('admins', 'admins.id', '=', 'purchases.created_by_id');
        if(!$request->has('order')) $query = $query->orderBy('purchases.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->with(['totalPurchase'=> $totalPurchase])->make(true);
    }

    
    
}