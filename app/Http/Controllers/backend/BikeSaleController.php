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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Auth;

class BikeSaleController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Bike Sales'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.bike-sales.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = BikeSale::find($id);
            $data['buyer'] = Buyer::find($data['item']->buyer_id);
            $select = [
                'bike_purchases.total_cost',
                'bike_models.name as model_name',
                'colors.name as color_name',
                'bikes.registration_no',
                'bikes.chassis_no',
                'bikes.engine_no',
            ];
            $data['bike_info'] = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                                ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                                ->join('colors', 'colors.id', '=', 'bikes.color_id')
                                ->where(['bike_purchases.id'=>$data['item']->bike_purchase_id,'purchase_status'=>1,'selling_status'=>0])
                                ->select($select)->first()->toArray();
            
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        $data['paymentMethods'] = $this->paymentMethods();
        $select = [
            'bike_purchases.id as bike_purchase_id',
            'bike_purchases.total_cost',
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
                            ->where(['purchase_status'=>1,'selling_status'=>0])
                            ->select($select)->get()->toArray();

        return view('backend.bike-sales.create-edit',compact('data'));
    }
    public function invoice($id, $print=null)
    {
        $data['breadcrumb'] = $this->breadcrumb;
        $data['print'] = $print;

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

            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bikes.engine_no',
            'bike_sales.buyer_id',
            'bike_sales.sale_date',
            'bike_sales.sale_price',
            'bike_sales.note',
            'bike_sales.reference_number',
            'bike_sales.status',
            'bike_sales.created_by_id',
        ];

        $data['basicInfo'] = BasicInfo::first()->toArray();
        $data['master'] = BikeSale::join('bike_purchases', 'bike_purchases.id', '=', 'bike_sales.bike_purchase_id')
                                ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                                ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                                ->join('colors', 'colors.id', '=', 'bikes.color_id')
                                ->join('accounts', 'accounts.id', '=', 'bike_sales.account_id')
                                ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                                ->join('buyers', 'buyers.id', '=', 'bike_sales.buyer_id')
                                ->join('admins', 'admins.id', '=', 'bike_sales.created_by_id')
                                ->where('bike_sales.id', $id)
                                ->select($select)->first()->toArray();
        $data['master']['invoice_no'] = $this->formatNumber($data['master']['id']);
        return view('backend.bike-sales.invoice',compact('data'));
    }
    public function store(Request $request)
    {
        $data = $request->all();

        $buyer_data['buyer_id'] = $request->buyer_id;
        $buyer_data['name'] = $request->buyer_name;
        $buyer_data['contact'] = $request->contact;
        $buyer_data['dob'] = $request->dob;
        $buyer_data['nid'] = $request->nid;
        $buyer_data['dl_no'] = $request->dl_no;
        $buyer_data['passport_no'] = $request->passport_no;
        $buyer_data['bcn_no'] = $request->bcn_no;

        $sale_data['bike_purchase_id'] = $request->bike_purchase_id;
        $sale_data['sale_date'] = $request->sale_date;
        $sale_data['account_id'] = $request->account_id;
        $sale_data['sale_price'] = $request->sale_price;
        $sale_data['reference_number'] = $request->reference_number;
        $sale_data['note'] = $request->note;
        $sale_data['buyer_id'] = $this->createOrUpdateBuyer($buyer_data);
        $sale_data['status'] = 0;

        $sale_data['doc_nid'] = $request->doc_nid;
        $sale_data['doc_reg_card'] = $request->doc_reg_card;
        $sale_data['doc_image'] = $request->doc_image;
        $sale_data['doc_deed'] = $request->doc_deed;
        $sale_data['doc_tax_token'] = $request->doc_tax_token;

        if(isset($sale_data['doc_nid'])){
            $sale_data['doc_nid'] = $this->documentUpload($sale_data['doc_nid']);
        }
        if(isset($sale_data['doc_reg_card'])){
            $sale_data['doc_reg_card'] = $this->documentUpload($sale_data['doc_reg_card']);
        }
        if(isset($sale_data['doc_image'])){
            $sale_data['doc_image'] = $this->documentUpload($sale_data['doc_image']);
        }
        if(isset($sale_data['doc_deed'])){
            $sale_data['doc_deed'] = $this->documentUpload($sale_data['doc_deed']);
        }
        if(isset($sale_data['doc_tax_token'])){
            $sale_data['doc_tax_token'] = $this->documentUpload($sale_data['doc_tax_token']);
        }

        BikeSale::create($sale_data);
        return redirect()->route('bike-sales.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }
    public function documentUpload($doc)
    {
        $doc_name = 'doc-'. Str::uuid().'.'.$doc->getClientOriginalExtension();
        $doc->move(public_path('uploads/'. 'bike-sales'), $doc_name);
        return $doc_name;
    }


    public function update(Request $request,$id)
    {
        $bikeSale = BikeSale::find($id);
        $data = $request->all();

        $buyer_data['buyer_id'] = $request->buyer_id;
        $buyer_data['name'] = $request->buyer_name;
        $buyer_data['contact'] = $request->contact;
        $buyer_data['dob'] = $request->dob;
        $buyer_data['nid'] = $request->nid;
        $buyer_data['dl_no'] = $request->dl_no;
        $buyer_data['passport_no'] = $request->passport_no;
        $buyer_data['bcn_no'] = $request->bcn_no;

        $sale_data['bike_purchase_id'] = $request->bike_purchase_id;
        $sale_data['sale_date'] = $request->sale_date;
        $sale_data['account_id'] = $request->account_id;
        $sale_data['sale_price'] = $request->sale_price;
        $sale_data['reference_number'] = $request->reference_number;
        $sale_data['note'] = $request->note;
        $sale_data['buyer_id'] = $this->createOrUpdateBuyer($buyer_data);
        $sale_data['status'] = 0;

        $doc_nid = $request->doc_nid;
        $doc_reg_card = $request->doc_reg_card;
        $doc_image = $request->doc_image;
        $doc_deed = $request->doc_deed;
        $doc_tax_token = $request->doc_tax_token;

        if (isset($doc_nid)) {
            $oldFile = public_path('uploads/bike-sales/' . $bikeSale->doc_nid);
            if (!empty($bikeSale->doc_nid) && File::exists($oldFile)) {
                File::delete($oldFile);
            }
            $sale_data['doc_nid'] = $this->documentUpload($doc_nid);
        }
        if (isset($doc_reg_card)) {
            $oldFile = public_path('uploads/bike-sales/' . $bikeSale->doc_reg_card);
            if (!empty($bikeSale->doc_reg_card) && File::exists($oldFile)) {
                File::delete($oldFile);
            }
            $sale_data['doc_reg_card'] = $this->documentUpload($doc_reg_card);
        }
        if (isset($doc_image)) {
            $oldFile = public_path('uploads/bike-sales/' . $bikeSale->doc_image);
            if (!empty($bikeSale->doc_image) && File::exists($oldFile)) {
                File::delete($oldFile);
            }
            $sale_data['doc_image'] = $this->documentUpload($doc_image);
        }
        if (isset($doc_deed)) {
            $oldFile = public_path('uploads/bike-sales/' . $bikeSale->doc_deed);
            if (!empty($bikeSale->doc_deed) && File::exists($oldFile)) {
                File::delete($oldFile);
            }
            $sale_data['doc_deed'] = $this->documentUpload($doc_deed);
        }
        if (isset($doc_tax_token)) {
            $oldFile = public_path('uploads/bike-sales/' . $bikeSale->doc_tax_token);
            if (!empty($bikeSale->doc_tax_token) && File::exists($oldFile)) {
                File::delete($oldFile);
            }
            $sale_data['doc_tax_token'] = $this->documentUpload($doc_tax_token);
        }
        $bikeSale->update($sale_data);
        return redirect()->route('bike-sales.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {
        $select = 
        [
            'bike_sales.id',
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'buyers.name as buyer_name',
            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bike_sales.buyer_id',
            'bike_sales.sale_date',
            'bike_sales.sale_price',
            'bike_sales.doc_nid',
            'bike_sales.doc_reg_card',
            'bike_sales.doc_image',
            'bike_sales.doc_deed',
            'bike_sales.doc_tax_token',
            'bike_sales.note',
            'bike_sales.reference_number',
            'bike_sales.name_transfer_date',
            'bike_sales.is_name_transfered',
            'bike_sales.is_repurchased',
            'bike_sales.status',
            'bike_sales.created_by_id',
        ];
        $query = BikeSale::join('bike_purchases', 'bike_purchases.id', '=', 'bike_sales.bike_purchase_id')
                            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                            ->join('colors', 'colors.id', '=', 'bikes.color_id')
                            ->join('accounts', 'accounts.id', '=', 'bike_sales.account_id')
                            ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->join('buyers', 'buyers.id', '=', 'bike_sales.buyer_id')
                            ->join('admins', 'admins.id', '=', 'bike_sales.created_by_id');

         if(!$request->has('order')){
            $query = $query->orderBy('bike_sales.status','asc')
                    ->orderBy('bike_sales.is_name_transfered','asc')
                    ->orderBy('bike_sales.sale_date','desc');
        }

        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }
    public function destroy($id)
    {
        $bikeSale = BikeSale::find($id);
        $imageArray = 
        [
            'doc_nid',
            'doc_reg_card',
            'doc_image',
            'doc_deed',
            'doc_tax_token',
        ];

        for ($i=0; $i < count($imageArray); $i++) {
            $oldFile = public_path('uploads/bike-sales/' . $bikeSale[$imageArray[$i]]);
            if (!empty($bikeSale[$imageArray[$i]]) && File::exists($oldFile)) {
                File::delete($oldFile);
            }
        }
        $bikeSale->delete();
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }



    public function approve($id)
    {
        $sale = BikeSale::find($id);
        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found.'], 404);
        }

        try {
            DB::beginTransaction(); // Start Transaction

            // Fetch the associated BikePurchase record
            $bikePurchase = BikePurchase::findOrFail($sale->bike_purchase_id);

            // Prepare account transaction data
            $accountData = [
                'account_id' => $sale->account_id,
                'credit_amount' => $sale->sale_price,
                'reference_number' => $sale->reference_number,
                'description' => 'Bike Sales',
                'transaction_date' => $sale->sale_date,
            ];
            $this->accountTransaction($accountData);

            if ($bikePurchase->investor_id == 1) {
                $investorData = [
                    'investor_id' => $bikePurchase->investor_id,
                    'account_id' => $sale->account_id,
                    'credit_amount' => $sale->sale_price,
                    'transaction_date' => $sale->sale_date,
                    'particular' => "Bike Sales",
                    'reference_number' => $sale->reference_number,
                ];
                $this->investorLedger($investorData);
            }else{
                $investorData = [
                    'investor_id' => $bikePurchase->investor_id,
                    'account_id' => $sale->account_id,
                    'credit_amount' => $bikePurchase->purchase_price,
                    'transaction_date' => $sale->sale_date,
                    'particular' => "Bike Sales",
                    'reference_number' => $sale->reference_number,
                ];
                $this->investorLedger($investorData);

                $investorData = [
                    'investor_id' => 1,
                    'account_id' => $sale->account_id,
                    'credit_amount' => ($sale->sale_price - $bikePurchase->purchase_price),
                    'transaction_date' => $sale->sale_date,
                    'particular' => "Bike Sales Profit",
                    'reference_number' => $sale->reference_number,
                ];
                $this->investorLedger($investorData);
            }


            $bikeProfit = 
            [
                'bike_sale_id'=> $sale->id,
                'investor_id'=> $bikePurchase->investor_id,
                'profit_amount'=> $sale->sale_price - $bikePurchase->total_cost,
                'profit_entry_date'=> $sale->sale_date,
            ];

            BikeProfit::create($bikeProfit);

            // Update sale and bike purchase statuses
            $sale->updateOrFail(['status' => 1]);
            $bikePurchase->updateOrFail(['selling_status' => 1]);

            DB::commit(); // Commit Transaction (Apply Changes)

            return response()->json(['success' => true, 'message' => 'Sales approved successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction (Undo Changes)
            \Log::error('Approval failed for Sale ID ' . $id . ': ' . $e->getMessage()); // Log the error

            return response()->json(['success' => false, 'message' => 'Approval failed.', 'error' => $e->getMessage()], 500);
        }
    }
    public function nameTransfer($id, $date)
    {
        try {
            DB::beginTransaction();
            $sale = BikeSale::find($id)->update(['is_name_transfered'=> 1,'name_transfer_date'=>$date]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Name Transfered Successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Name Transfered failed.', 'error' => $e->getMessage()], 500);
        }
    }

    
    public function createOrUpdateBuyer($data)
    {
        $buyer = Buyer::updateOrCreate(['id'=> $data['buyer_id']], $data);
        return $buyer->id;
    }

    public function buyerSearch(Request $request)
    {
        $search = $request->input('search');
        $buyers = Buyer::where(['status'=>1])
                    ->where(function ($query) use ($search) {
                        $query->where('dl_no', 'LIKE', "%$search%")
                        ->orWhere('nid', 'LIKE', "%$search%")
                        ->orWhere('passport_no', 'LIKE', "%$search%")
                        ->orWhere('bcn_no', 'LIKE', "%$search%")
                        ->orWhere('contact', 'LIKE', "%$search%");
                    })
                    ->select('id','name','contact','nid','dob','dl_no','passport_no','bcn_no','status')
                    ->get()->toArray();
        $data = [];
        foreach ($buyers as $buyer) {
            $data[] = [
                "label" => 'Name: '. $buyer['name'] .' DL#' . $buyer['dl_no'].' NID#' . $buyer['nid'].' PP#' . $buyer['passport_no'].' BCN#' . $buyer['bcn_no'],
                "id"=> $buyer['id'],
                "name"=> $buyer['name'],
                "contact"=> $buyer['contact'],
                "nid"=> $buyer['nid'],
                "dob"=> $buyer['dob'],
                "dl_no"=> $buyer['dl_no'],
                "passport_no"=> $buyer['passport_no'],
                "bcn_no"=> $buyer['bcn_no'],
                "status"=> $buyer['status'],
            ];
        }

        return response()->json($data);
    }



}
