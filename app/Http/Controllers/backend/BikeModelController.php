<?php

namespace App\Http\Controllers\backend;

use App\Models\BikeModel;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class BikeModelController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Bike Models'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.bike-models.index', compact('data'));
    }
    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = BikeModel::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        $data['brands'] = Brand::where('status', 1)->select('id','name')->get()->toArray();
        return view('backend.bike-models.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by_id'] = $this->getUserId();
        BikeModel::create($data);
        return redirect()->route('bike-models.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request,$id)
    {
        $data = $request->all();
        $data['updated_by_id'] = $this->getUserId();
        BikeModel::find($id)->update($data);
        return redirect()->route('bike-models.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
    public function list(Request $request)
    {
        $select = 
        [
            'bike_models.id',
            'brands.name as brand_name',
            'bike_models.name as model_name',
            'bike_models.manufacture_year',
            'bike_models.engine_capacity',
            'bike_models.status',
        ];
        $query = BikeModel::join('brands', 'brands.id', '=', 'bike_models.brand_id');
        if(!$request->has('order')) $query = $query->orderBy('bike_models.id','desc');
        $query = $query->select($select);
        return DataTables::of($query)->make(true);
    }

}
