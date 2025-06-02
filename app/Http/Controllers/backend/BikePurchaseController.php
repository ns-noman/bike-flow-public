<?php

namespace App\Http\Controllers\backend;

use App\Models\Investor;
use App\Models\BikePurchase;
use App\Models\BikeSale;
use App\Models\PaymentMethod;
use App\Models\Brand;
use App\Models\BikeModel;
use App\Models\Color;
use App\Models\Seller;
use App\Models\Buyer;
use App\Models\Bike;
use App\Models\BasicInfo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Auth;

class BikePurchaseController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Bike Purchases'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.bike-purchases.index', compact('data'));
    }
    public function createOrEdit($id=0, $sale_id = 0)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = BikePurchase::find($id);
            $data['seller'] = Seller::find($data['item']->seller_id)->toArray();
            $data['broker'] = Seller::find($data['item']->broker_id);
            $data['bike'] = Bike::find($data['item']->bike_id)->toArray();
            $data['bike']['brand_id'] = BikeModel::find($data['bike']['model_id'])->brand_id;
            $data['models'] = BikeModel::where(['status'=>1, 'brand_id'=> $data['bike']['brand_id']])->select('id','name')->get()->toArray();
        }elseif($sale_id){
            $data['title'] = 'Repurchase';
            $data['sale_id'] = $sale_id;
            $sale = BikeSale::find($sale_id);
            $bike_purchase = BikePurchase::find($sale->bike_purchase_id);
            $data['seller'] = Buyer::where('id',$sale->buyer_id)->select(['id','name','contact','nid','dob','dl_no','passport_no','bcn_no'])->first()->toArray();
            $data['bike'] = Bike::find($bike_purchase->bike_id)->toArray();
            $data['bike']['brand_id'] = BikeModel::find($data['bike']['model_id'])->brand_id;
            $data['models'] = BikeModel::where(['status'=>1, 'brand_id'=> $data['bike']['brand_id']])->select('id','name')->get()->toArray();
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        $data['paymentMethods'] = $this->paymentMethods();
        $data['investors'] = Investor::where('status',1)->get();
        $data['brands'] = Brand::where('status', 1)->select('id','name')->get()->toArray();
        $data['colors'] = Color::where('status', 1)->select('id','name')->get()->toArray();

        return view('backend.bike-purchases.create-edit',compact('data'));
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $bikeId = $request->bike_id ?? null;

            // Validation for bike fields
            $request->validate([
                'model_id'         => 'required',
                'color_id'         => 'required',
                'manufacture_year' => 'required',
                'chassis_no'       => 'required|unique:bikes,chassis_no,' . $bikeId,
                'engine_no'        => 'required|unique:bikes,engine_no,' . $bikeId,
            ], [
                'model_id.required'         => 'Model is required.',
                'color_id.required'         => 'Color is required.',
                'manufacture_year.required' => 'Manufacture year is required.',
                'chassis_no.required'       => 'Chassis number is required.',
                'chassis_no.unique'         => 'This chassis number already exists.',
                'engine_no.required'        => 'Engine number is required.',
                'engine_no.unique'          => 'This engine number already exists.',
            ]);


            $data = $request->all();
            // Seller Data
            if ($data['sale_id']) {
                $seller_data = Buyer::where('id',$data['buyer_id'])->select(['name','contact','nid','dob','dl_no','passport_no','bcn_no'])->first()->toArray();
                $seller_data['seller_id'] = null;
            }else{
                $seller_data = [
                    'seller_id'   => $request->seller_id,
                    'name'        => $request->seller_name,
                    'contact'     => $request->contact,
                    'dob'         => $request->dob,
                    'nid'         => $request->nid,
                    'dl_no'       => $request->dl_no,
                    'passport_no' => $request->passport_no,
                    'bcn_no'      => $request->bcn_no,
                ];
            }



            // Broker Data
            $broker_data = [
                'broker_id'   => $request->broker_id,
                'name'        => $request->broker_name,
                'contact'     => $request->broker_contact,
                'seller_type' => 1, // 1 for broker
            ];

            // Bike Data
            if ($data['sale_id']) {
                $sale = BikeSale::find($data['sale_id']);
                $bike_purchase = BikePurchase::find($sale->bike_purchase_id);
                $bike_id = $bike_purchase->bike_id;
            }else{
                $bike_data = [
                    'bike_id'         => $request->bike_id,
                    'brand_id'        => $request->brand_id,
                    'model_id'        => $request->model_id,
                    'color_id'        => $request->color_id,
                    'manufacture_year'=> $request->manufacture_year,
                    'chassis_no'      => $request->chassis_no,
                    'engine_no'       => $request->engine_no,
                    'registration_no' => $request->registration_no,
                ];
             }

            // Purchase Data
            $purchase_data = [
                'transaction_type'  => $request->transaction_type,
                'investor_id'       => $request->investor_id,
                'purchase_date'     => $request->purchase_date,
                'account_id'        => $request->account_id,
                'purchase_price'    => $request->purchase_price,
                'total_cost'        => $request->purchase_price,
                'reference_number'  => $request->reference_number,
                'note'              => $request->note,
                'seller_id'         => $this->createOrUpdateSeller($seller_data),
                'status'            => 0,
            ];

            // If broker data exists, add broker_id
            if ($broker_data['broker_id']) {
                $purchase_data['broker_id'] = $this->createOrUpdateBroker($broker_data);
            }

            // Assign Bike ID

            if ($data['sale_id']) {
                $purchase_data['bike_id'] = $bike_id;
                $purchase_data['bike_sale_id'] = $data['sale_id'];
            }else{
                $purchase_data['bike_id'] = $this->createOrUpdateBike($bike_data);
            }
            $purchase_data['doc_nid'] = $request->doc_nid;
            $purchase_data['doc_reg_card'] = $request->doc_reg_card;
            $purchase_data['doc_image'] = $request->doc_image;
            $purchase_data['doc_deed'] = $request->doc_deed;
            $purchase_data['doc_tax_token'] = $request->doc_tax_token;

            if(isset($purchase_data['doc_nid'])){
                $purchase_data['doc_nid'] = $this->documentUpload($purchase_data['doc_nid']);
            }
            if(isset($purchase_data['doc_reg_card'])){
                $purchase_data['doc_reg_card'] = $this->documentUpload($purchase_data['doc_reg_card']);
            }
            if(isset($purchase_data['doc_image'])){
                $purchase_data['doc_image'] = $this->documentUpload($purchase_data['doc_image']);
            }
            if(isset($purchase_data['doc_deed'])){
                $purchase_data['doc_deed'] = $this->documentUpload($purchase_data['doc_deed']);
            }
            if(isset($purchase_data['doc_tax_token'])){
                $purchase_data['doc_tax_token'] = $this->documentUpload($purchase_data['doc_tax_token']);
            }

            // Save Purchase Data
            BikePurchase::create($purchase_data);

            DB::commit();

            return redirect()
                ->route('bike-purchases.index')
                ->with('alert', [
                    'messageType' => 'success',
                    'message'     => 'Data Inserted Successfully!',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function documentUpload($doc)
    {
        $doc_name = 'doc-'. Str::uuid().'.'.$doc->getClientOriginalExtension();
        $doc->move(public_path('uploads/'. 'bike-purchases'), $doc_name);
        return $doc_name;
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
    
            $bikePurchase = BikePurchase::find($id);

            // Seller Data
            $seller_data = [
                'seller_id'   => $request->seller_id,
                'name'        => $request->seller_name,
                'contact'     => $request->contact,
                'dob'         => $request->dob,
                'nid'         => $request->nid,
                'dl_no'       => $request->dl_no,
                'passport_no' => $request->passport_no,
                'bcn_no'      => $request->bcn_no,
            ];

    
            // Broker Data
            $broker_data = [
                'broker_id'   => $request->broker_id,
                'name'        => $request->broker_name,
                'contact'     => $request->broker_contact,
                'seller_type' => 1, // 1 for broker
            ];
    
            // Bike Data
            $bike_data = [
                'bike_id'         => $request->bike_id,
                'brand_id'        => $request->brand_id,
                'model_id'        => $request->model_id,
                'color_id'        => $request->color_id,
                'manufacture_year'=> $request->manufacture_year,
                'chassis_no'      => $request->chassis_no,
                'engine_no'       => $request->engine_no,
                'registration_no' => $request->registration_no,
            ];
    
            // Purchase Data
            $purchase_data = [
                'transaction_type'  => $request->transaction_type,
                'investor_id'       => $request->investor_id,
                'purchase_date'     => $request->purchase_date,
                'account_id'        => $request->account_id,
                'purchase_price'    => $request->purchase_price,
                'total_cost'        => $request->purchase_price,
                'reference_number'  => $request->reference_number,
                'note'              => $request->note,
                'seller_id'         => $this->createOrUpdateSeller($seller_data),
                'status'            => 0,
            ];
    
            // If broker data exists, add broker_id
            if ($broker_data['broker_id']) {
                $purchase_data['broker_id'] = $this->createOrUpdateBroker($broker_data);
            }
    
            // Assign Bike ID
            $purchase_data['bike_id'] = $this->createOrUpdateBike($bike_data);


            $doc_nid = $request->doc_nid;
            $doc_reg_card = $request->doc_reg_card;
            $doc_image = $request->doc_image;
            $doc_deed = $request->doc_deed;
            $doc_tax_token = $request->doc_tax_token;
    
    
    
            if (isset($doc_nid)) {
                $oldFile = public_path('uploads/bike-purchases/' . $bikePurchase->doc_nid);
                if (!empty($bikePurchase->doc_nid) && File::exists($oldFile)) {
                    File::delete($oldFile);
                }
                $purchase_data['doc_nid'] = $this->documentUpload($doc_nid);
            }
            if (isset($doc_reg_card)) {
                $oldFile = public_path('uploads/bike-purchases/' . $bikePurchase->doc_reg_card);
                if (!empty($bikePurchase->doc_reg_card) && File::exists($oldFile)) {
                    File::delete($oldFile);
                }
                $purchase_data['doc_reg_card'] = $this->documentUpload($doc_reg_card);
            }
            if (isset($doc_image)) {
                $oldFile = public_path('uploads/bike-purchases/' . $bikePurchase->doc_image);
                if (!empty($bikePurchase->doc_image) && File::exists($oldFile)) {
                    File::delete($oldFile);
                }
                $purchase_data['doc_image'] = $this->documentUpload($doc_image);
            }
            if (isset($doc_deed)) {
                $oldFile = public_path('uploads/bike-purchases/' . $bikePurchase->doc_deed);
                if (!empty($bikePurchase->doc_deed) && File::exists($oldFile)) {
                    File::delete($oldFile);
                }
                $purchase_data['doc_deed'] = $this->documentUpload($doc_deed);
            }
            if (isset($doc_tax_token)) {
                $oldFile = public_path('uploads/bike-purchases/' . $bikePurchase->doc_tax_token);
                if (!empty($bikePurchase->doc_tax_token) && File::exists($oldFile)) {
                    File::delete($oldFile);
                }
                $purchase_data['doc_tax_token'] = $this->documentUpload($doc_tax_token);
            }

            // Update Purchase Data
            $bikePurchase->update($purchase_data);
    
            DB::commit();
    
            return redirect()
                ->route('bike-purchases.index')
                ->with('alert', [
                    'messageType' => 'success',
                    'message'     => 'Data Updated Successfully!',
                ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function invoice($id, $print=null)
    {
        $data['breadcrumb'] = $this->breadcrumb;
        $data['print'] = $print;

        $select = 
        [
            'bike_purchases.id',
            'investors.name as investor_name',
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'sellers.name as seller_name',
            'sellers.contact as seller_contact',
            'sellers.nid as seller_nid',
            'sellers.dl_no as seller_dl_no',
            'sellers.passport_no as seller_passport_no',
            'sellers.bcn_no as seller_bcn_no',


            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bikes.engine_no',
            'bikes.manufacture_year',
            'bike_purchases.seller_id',
            'bike_purchases.purchase_price',
            'bike_purchases.servicing_cost',
            'bike_purchases.total_cost',
            'bike_purchases.purchase_date',
            'bike_purchases.note',
            'bike_purchases.reference_number',
            'bike_purchases.purchase_status',
            'bike_purchases.selling_status',
            'bike_purchases.created_by_id',
            'bike_purchases.updated_by_id',
        ];

        $data['basicInfo'] = BasicInfo::first()->toArray();
        $data['master'] = BikePurchase::join('investors', 'investors.id', '=', 'bike_purchases.investor_id')
                                    ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                                    ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                                    ->join('colors', 'colors.id', '=', 'bikes.color_id')
                                    ->join('accounts', 'accounts.id', '=', 'bike_purchases.account_id')
                                    ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                                    ->join('sellers', 'sellers.id', '=', 'bike_purchases.seller_id')
                                    ->join('admins', 'admins.id', '=', 'bike_purchases.created_by_id')
                                    ->where('bike_purchases.id', $id)
                                    ->select($select)->first()->toArray();
        $data['master']['invoice_no'] = $this->formatNumber($data['master']['id']);
        return view('backend.bike-purchases.invoice',compact('data'));
    }
 
    public function list(Request $request)
    {
        $select = [
             'bike_purchases.id',
            'investors.name as investor_name',
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'sellers.name as seller_name',
            'admins.name as creator_name',
            'accounts.account_no',
            'payment_methods.name as payment_method',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bike_purchases.seller_id',
            'bike_purchases.purchase_price',
            'bike_purchases.servicing_cost',
            'bike_purchases.total_cost',
            'bike_purchases.purchase_date',
            'bike_purchases.doc_nid',
            'bike_purchases.doc_reg_card',
            'bike_purchases.doc_image',
            'bike_purchases.doc_deed',
            'bike_purchases.doc_tax_token',
            'bike_purchases.note',
            'bike_purchases.reference_number',
            'bike_purchases.purchase_status',
            'bike_purchases.selling_status',
            'bike_purchases.created_by_id',
            'bike_purchases.updated_by_id',
        ];
        $query = BikePurchase::join('investors', 'investors.id', '=', 'bike_purchases.investor_id')
            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
            ->join('colors', 'colors.id', '=', 'bikes.color_id')
            ->join('accounts', 'accounts.id', '=', 'bike_purchases.account_id')
            ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
            ->join('sellers', 'sellers.id', '=', 'bike_purchases.seller_id')
            ->join('admins', 'admins.id', '=', 'bike_purchases.created_by_id');
        if (!$request->has('order')) {
            $query = $query->orderBy('bike_purchases.id', 'desc');
        }

        $query = $query->select($select);

        return DataTables::of($query)
                ->filter(function ($query) use ($request) {
                    if ($search = $request->input('search.value')) {
                        $query->where(function ($q) use ($search) {
                            $q->where('investors.name', 'like', "%{$search}%")
                            ->orWhere('bike_models.name', 'like', "%{$search}%")
                            ->orWhere('colors.name', 'like', "%{$search}%")
                            ->orWhere('sellers.name', 'like', "%{$search}%")
                            ->orWhere('accounts.account_no', 'like', "%{$search}%")
                            ->orWhere('bikes.registration_no', 'like', "%{$search}%")
                            ->orWhere('bikes.chassis_no', 'like', "%{$search}%")
                            ->orWhere('bike_purchases.reference_number', 'like', "%{$search}%")
                            ->orWhere('bike_purchases.note', 'like', "%{$search}%");
                        });
                    }
                })
                ->make(true);

    }
    public function destroy($id)
    {
        $bikePurchase = BikePurchase::find($id);

        $imageArray = 
        [
            'doc_nid',
            'doc_reg_card',
            'doc_image',
            'doc_deed',
            'doc_tax_token',
        ];

        for ($i=0; $i < count($imageArray); $i++) {
            $oldFile = public_path('uploads/bike-purchases/' . $bikePurchase[$imageArray[$i]]);
            if (!empty($bikePurchase[$imageArray[$i]]) && File::exists($oldFile)) {
                File::delete($oldFile);
            }
        }
        $bikePurchase->delete();
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }

    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $purchase = BikePurchase::findOrFail($id);
            if($purchase->bike_sale_id){
                BikeSale::findOrFail($purchase->bike_sale_id)->update(['is_repurchased'=> 1]);
            }
            $account_id = $purchase->account_id;
            $investor_id = $purchase->investor_id;
            $debit_amount = $purchase->purchase_price;
            $date = $purchase->purchase_date;
            $reference_number = $purchase->reference_number;

            // Update purchase status
            $purchase->update(['purchase_status' => 1]);

            // Account Transaction
            $accountData = [
                'account_id'        => $account_id,
                'debit_amount'      => $debit_amount,
                'credit_amount'     => null,
                'reference_number'  => $reference_number,
                'description'       => 'Bike Purchase',
                'transaction_date'  => $date, 
            ];
            $this->accountTransaction($accountData);

            // Investor Ledger
            $investorLedger = [
                'investor_id'       => $investor_id,
                'account_id'        => $account_id,
                'debit_amount'      => $debit_amount,
                'transaction_date'  => $date, 
                'particular'       => "Bike Purchase",
                'reference_number'  => $reference_number,
            ];
            $this->investorLedger($investorLedger);

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


    public function models($brand_id)
    {
        $data = BikeModel::where(['status'=>1, 'brand_id'=> $brand_id])->select('id','name')->get()->toArray();
        return response()->json($data, 200);
    }
    public function createOrUpdateSeller($data)
    {
        $seller = Seller::updateOrCreate(['id'=> $data['seller_id']], $data);
        return $seller->id;
    }
    public function createOrUpdateBroker($data)
    {
        $broker = Seller::updateOrCreate(['id'=> $data['broker_id']], $data);
        return $broker->id;
    }
    public function createOrUpdateBike($data)
    {
        $bike = Bike::updateOrCreate(['id'=> $data['bike_id'],'registration_no'=>$data['registration_no']], $data);
        return $bike->id;
    }
    public function sellerSearch(Request $request)
    {
        $search = $request->input('search');
        $sellers = Seller::where(['seller_type'=>0, 'status'=>1])
                    ->where(function ($query) use ($search) {
                        $query->where('dl_no', 'LIKE', "%$search%")
                        ->orWhere('nid', 'LIKE', "%$search%")
                        ->orWhere('passport_no', 'LIKE', "%$search%")
                        ->orWhere('bcn_no', 'LIKE', "%$search%")
                        ->orWhere('contact', 'LIKE', "%$search%");
                    })
                    ->select('id','name','contact','nid','dob','dl_no','passport_no','bcn_no','status','seller_type')
                    ->get()->toArray();
        $data = [];
        foreach ($sellers as $seller) {
            $data[] = [
                "label" => 'Name: '. $seller['name'] .' DL#' . $seller['dl_no'].' NID#' . $seller['nid'].' PP#' . $seller['passport_no'].' BCN#' . $seller['bcn_no'],
                "id"=> $seller['id'],
                "name"=> $seller['name'],
                "contact"=> $seller['contact'],
                "nid"=> $seller['nid'],
                "dob"=> $seller['dob'],
                "dl_no"=> $seller['dl_no'],
                "passport_no"=> $seller['passport_no'],
                "bcn_no"=> $seller['bcn_no'],
                "status"=> $seller['status'],
            ];
        }

        return response()->json($data);
    }
    public function brokerSearch(Request $request)
    {
        $search = $request->input('search');
        $brokers = Seller::where(['seller_type'=>1, 'status'=>1])
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%")
                            ->orWhere('contact', 'LIKE', "%$search%");
                    })
                    ->select('id','name','contact')
                    ->get()->toArray();
        $data = [];
        foreach ($brokers as $brokers) {
            $data[] = [
                "label" => 'Name:- '. $brokers['name'] .' Contact:- ' . $brokers['contact'],
                "id"=> $brokers['id'],
                "name"=> $brokers['name'],
                "contact"=> $brokers['contact'],
            ];
        }

        return response()->json($data);
    }
    public function bikeSearch(Request $request)
    {
        $search = $request->input('search');

        $bikes = Bike::join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                    ->join('colors', 'colors.id', '=', 'bikes.color_id')
                    ->where(function ($query) use ($search) {
                        $query->where('bikes.registration_no', 'like', "%$search%")
                            ->orWhere('bikes.chassis_no', 'like', "%$search%")
                            ->orWhere('bikes.engine_no', 'like', "%$search%");
                    })
                    ->select([
                        'bikes.id as bike_id',
                        'bike_models.brand_id',
                        'bike_models.id as model_id',
                        'colors.id as color_id',
                        'bike_models.name as model_name',
                        'colors.name as color_name',
                        'bikes.registration_no',
                        'bikes.chassis_no',
                        'bikes.engine_no',
                        'bikes.manufacture_year',
                    ])
                    ->limit(5)
                    ->get();

        $data = $bikes->map(function ($bike) {
            return [
                'label' => "{$bike->model_name} rg#{$bike->registration_no}, ch#{$bike->chassis_no}, en#{$bike->engine_no}",
                'id' => $bike->bike_id,
                'brand_id' => $bike->brand_id,
                'model_id' => $bike->model_id,
                'color_id' => $bike->color_id,
                'model_name' => $bike->model_name,
                'color_name' => $bike->color_name,
                'registration_no' => $bike->registration_no,
                'chassis_no' => $bike->chassis_no,
                'engine_no' => $bike->engine_no,
                'manufacture_year' => $bike->manufacture_year,
            ];
        });

        return response()->json($data);
    }


}
