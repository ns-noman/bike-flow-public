<?php

namespace App\Http\Controllers\backend;

use App\Models\Investor;
use App\Models\BikeServiceRecord;
use App\Models\BikeServiceRecordDetails;
use App\Models\BikePurchase;
use App\Models\BikeService;
use App\Models\BasicInfo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class BikeServiceRecordController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Bike Service Records'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.bike-service-records.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = BikeServiceRecord::find($id);
            $data['bike_service_record_details'] = BikeServiceRecordDetails::join('bike_services','bike_services.id','=','bike_service_record_details.service_id')
                                                        ->where('bike_service_record_id', $id)
                                                        ->select('bike_service_record_details.*', 'bike_services.name as service_name')
                                                        ->get()->toArray();
        }else{
            $data['title'] = 'Create';
        }
        $select = [
                    'bike_purchases.id as bike_purchase_id',
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
        $data['breadcrumb'] = $this->breadcrumb;
        $data['paymentMethods'] = $this->paymentMethods();
        $data['bikes'] = $bikes = BikePurchase::join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                                        ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                                        ->join('colors', 'colors.id', '=', 'bikes.color_id')
                                        ->where(['purchase_status'=>1,'selling_status'=>0])
                                        ->select($select)->get()->toArray();
        $data['bike_services'] = BikeService::where('status',1)->get();
        return view('backend.bike-service-records.create-edit',compact('data'));
    }
    public function view($id, $print=null)
    {
        $data['title'] = 'Edit';
        $data['print'] = $print;

        $select = [
            'bike_service_records.*',
            'bike_service_records.id',
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'bikes.registration_no',
            'bikes.chassis_no',
        ];

        $data['basicInfo'] = BasicInfo::first()->toArray();
        $data['master'] = BikeServiceRecord::join('bike_purchases', 'bike_purchases.id', '=', 'bike_service_records.bike_purchase_id')
                                            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                                            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                                            ->join('colors', 'colors.id', '=', 'bikes.color_id')
                                            ->where('bike_service_records.id',$id)
                                            ->select($select)
                                            ->first()->toArray();
                                            
        $data['master']['details'] = BikeServiceRecordDetails::join('bike_services','bike_services.id','=','bike_service_record_details.service_id')
                                                    ->where('bike_service_record_id', $id)
                                                    ->select('bike_service_record_details.*', 'bike_services.name as service_name')
                                                    ->get()->toArray();
        return view('backend.bike-service-records.view',compact('data'));
    }
    public function store(Request $request)
    {
        $bike_purchase_id = $request->bike_purchase_id;
        $date = $request->date;
        $total_amount = $request->total_amount;
        $tax_amount = $request->tax_amount;
        $note = $request->note;
        $service_id = $request->service_id;
        $price = $request->price;
        $quantity = $request->quantity;
        $invoice_no = $this->formatNumber(BikeServiceRecord::latest()->limit(1)->max('invoice_no')+1);

        //BikeServiceRecord create*****
        $bikeServiceRecord = new BikeServiceRecord();
        $bikeServiceRecord->bike_purchase_id = $bike_purchase_id;
        $bikeServiceRecord->invoice_no = $invoice_no;
        $bikeServiceRecord->date = $date;
        $bikeServiceRecord->total_amount = $total_amount;
        $bikeServiceRecord->note = $note;
        $bikeServiceRecord->status = 0;
        $bikeServiceRecord->save();
        //End*****
        for ($i=0; $i < count($service_id); $i++)
        {
            //BikeServiceRecord Details create*****
            $bikeServiceRecordDetails = new BikeServiceRecordDetails();
            $bikeServiceRecordDetails->bike_service_record_id = $bikeServiceRecord->id;
            $bikeServiceRecordDetails->service_id = $service_id[$i];
            $bikeServiceRecordDetails->price = $price[$i]; 
            $bikeServiceRecordDetails->quantity = $quantity[$i];
            $bikeServiceRecordDetails->save();
            //End*****
        }
        return redirect()->route('bike-service-records.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $bike_purchase_id = $request->bike_purchase_id;
        $date = $request->date;
        $total_amount = $request->total_amount;
        $tax_amount = $request->tax_amount;
        $note = $request->note;
        $service_id = $request->service_id;
        $price = $request->price;
        $quantity = $request->quantity;

        //BikeServiceRecord create*****
        $bikeServiceRecord = BikeServiceRecord::find($id);
        $bikeServiceRecord->bike_purchase_id = $bike_purchase_id;
        $bikeServiceRecord->date = $date;
        $bikeServiceRecord->total_amount = $total_amount;
        $bikeServiceRecord->note = $note;
        $bikeServiceRecord->status = 0;
        $bikeServiceRecord->save();
        BikeServiceRecordDetails::where('bike_service_record_id', $id)->delete();

        //End*****
        for ($i=0; $i < count($service_id); $i++)
        {
            //BikeServiceRecord Details create*****
            $bikeServiceRecordDetails = new BikeServiceRecordDetails();
            $bikeServiceRecordDetails->bike_service_record_id = $bikeServiceRecord->id;
            $bikeServiceRecordDetails->service_id = $service_id[$i];
            $bikeServiceRecordDetails->price = $price[$i]; 
            $bikeServiceRecordDetails->quantity = $quantity[$i];
            $bikeServiceRecordDetails->save();
            //End*****
        }

        return redirect()->route('bike-service-records.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
 
    public function list(Request $request)
    {
        $select = 
        [
            'bike_service_records.id',
            'bike_models.name as model_name',
            'colors.name as color_name',
            'colors.hex_code',
            'admins.name as creator_name',
            'bikes.registration_no',
            'bikes.chassis_no',
            'bike_service_records.invoice_no',
            'bike_service_records.date',
            'bike_service_records.total_amount', 
            'bike_service_records.note',
            'admins.name as created_by',
            'bike_service_records.status',
        ];
        $query = BikeServiceRecord::join('bike_purchases', 'bike_purchases.id', '=', 'bike_service_records.bike_purchase_id')
                            ->join('bikes', 'bikes.id', '=', 'bike_purchases.bike_id')
                            ->join('bike_models', 'bike_models.id', '=', 'bikes.model_id')
                            ->join('colors', 'colors.id', '=', 'bikes.color_id')
                            ->join('admins', 'admins.id', '=', 'bike_purchases.created_by_id');
        
        if(!$request->has('order')){
            $query = $query->orderBy('bike_service_records.status','asc');
            $query = $query->orderBy('bike_service_records.id','desc');
        }
        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }
    public function destroy($id)
    {
        BikeServiceRecord::destroy($id);
        return response()->json(['success'=>true,'message'=>'Data Deleted Successfully!'], 200);
    }

    public function approve($id)
    {
        $bikeServiceRecord = BikeServiceRecord::find($id);

        $bikePurchase = BikePurchase::find($bikeServiceRecord->bike_purchase_id);
        $oldServicing_cost = $bikePurchase->servicing_cost;
        $newServicing_cost = $oldServicing_cost + $bikeServiceRecord->total_amount;
        $total_cost = $bikePurchase->purchase_price + $newServicing_cost;
        $bikePurchase->servicing_cost = $newServicing_cost;
        $bikePurchase->total_cost = $total_cost;
        $bikePurchase->save();
        
        $bikeServiceRecord->update(['status'=> 1]);

        return response()->json(['success'=>true,'message'=>'Transaction approved successfully.'], 200);
    }

}
