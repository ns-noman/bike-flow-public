<?php

namespace App\Http\Controllers\backend\items;

use App\Models\Item;
use App\Models\StockHistory;
use App\Models\Unit;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\BasicInfo;
use App\Models\Investor;
use App\Models\InvestorTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use DB;

class ItemController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Items'];}
    public function index()
    {
        $data['category_types'] = CategoryType::get();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.items.items.index',compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Item::find($id);
            $data['categories'] = Category::where(['parent_cat_id'=>0, 'cat_type_id'=> $data['item']->cat_type_id])->where('status',1)->get();
            $data['sub_categories'] = Category::where('parent_cat_id',$data['item']->cat_id)->where('status',1)->get();

        }else{
            $data['title'] = 'Create';
        }
        $data['category_types'] = CategoryType::get();
        $data['units'] = Unit::where('status',1)->get();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.items.items.create-or-edit', compact('data'));
    }

    public function subCategory($id=null)
    {
        $sub_categories = Category::where('parent_cat_id',$id)->where('status',1)->select('id', 'title')->get()->toArray();
        return response()->json($sub_categories, 200);
    }
    public function categories($id)
    {
        $categories = Category::with(['subcategories'])
                                ->where(['parent_cat_id' => 0, 'cat_type_id' => $id])
                                ->where('status', 1)
                                ->select('id', 'title')
                                ->get()
                                ->toArray();
        return response()->json($categories, 200);
    }
    
    public function store(Request $request)
    {
        DB::beginTransaction();

        $data = $request->all();
        $data['cat_type_id'] = Category::find($data['cat_id'])->cat_type_id;
        if(isset($data['image'])){
            $fileName = 'item-'. time().'.'. $data['image']->getClientOriginalExtension();
            $data['image']->move(public_path('uploads/items'), $fileName);
            $data['image'] = $fileName;
        }
        $data['current_stock'] = $data['opening_stock'] ?? 0; 
        $item = Item::create($data);


        if($data['current_stock']){
            //Stock History Update
            $stockHistory = new StockHistory;
            $stockHistory->item_id = $item->id;
            $stockHistory->date = date('Y-m-d');
            $stockHistory->particular = 'Opening Stock';
            $stockHistory->stock_in_qty = $data['opening_stock'] ?? 0;
            $stockHistory->rate = $item->purchase_price;
            $stockHistory->current_stock = $data['opening_stock'] ?? 0;
            $stockHistory->created_by_id = Auth::guard('admin')->user()->id;

            $itemValue = $item->purchase_price * $data['opening_stock'];
            
            // Investor Transaction
            $currentBalance = InvestorTransaction::where(['status'=>1,'investor_id'=>1])->latest()->pluck('current_balance')->first() ?? 0;
            $newcurrentBalance = $itemValue + $currentBalance;

            $investor = Investor::find(1);
            $investor->investment_capital += $itemValue;
            $investor->save();
            
            $investor_transaction = [
                'investor_id'=> 1,
                'account_id'=> 2,
                'credit_amount'=> $itemValue,
                'current_balance'=> $newcurrentBalance,
                'transaction_date'=> date('Y-m-d'),
                'particular'=> 'Opening Stock Investment',
                'transaction_type'=> 1,
                'status'=> 1,
            ];
            InvestorTransaction::create($investor_transaction);

            // Investor Transaction
            $investorLedger = 
            [
                'investor_id'=> 1,
                'account_id'=> 2,
                'credit_amount'=> $itemValue,
                'transaction_date'=> date('Y-m-d'),
                'particular'=> 'Opening Stock Investment',
            ];
            $this->investorLedger($investorLedger);
            // Investor Transaction
            $investorLedger = 
            [
                'investor_id'=> 1,
                'account_id'=> 2,
                'debit_amount'=> $itemValue,
                'transaction_date'=> date('Y-m-d'),
                'particular'=> 'Opening Stock Purchase',
            ];
            $this->investorLedger($investorLedger);


        }
        $stockHistory->save();
        DB::commit();
        return redirect()->route('items.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    

    public function update(Request $request,$id)
    {
        $item = Item::find($id);
        $data = $request->all();
        $data['cat_type'] = Category::find($data['cat_id'])->cat_type;
        if(isset($data['image'])){
            $fileName = 'item-'. time().'.'. $data['image']->getClientOriginalExtension();
            $data['image']->move(public_path('uploads/items'), $fileName);
            $data['image'] = $fileName;
            if($item->product_image) unlink(public_path('uploads/items/'. $item->image));
        }
        $item->update($data);
        return redirect()->route('items.index')->with('alert',['messageType'=>'warning','message'=>'Data Updated Successfully!']);
    }

    public function destroy($id)
    {
        $stockHistory = StockHistory::where('item_id', $id)->exists();
        if($stockHistory) return redirect()->back()->with('alert',['messageType'=>'warning','message'=>'Data Deletion Failed!']);
        Item::destroy($id);
        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }
    
    public function list(Request $request)
    {

        $cat_type_id = $request->cat_type_id;
        $cat_id = $request->cat_id;
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
                        ->join('units', 'units.id', '=', 'items.unit_id');
        if($cat_type_id) $query = $query->where('items.cat_type_id', $cat_type_id);
        if($cat_id){
            $query = $query->where(function($query) use ($cat_id){
                $query->where('items.cat_id', $cat_id)
                        ->orWhere('items.sub_cat_id', $cat_id);
            });
        }

                        $query = $query->select($select);
        if(!$request->has('order')) $query = $query->orderBy('items.id','desc');
        return DataTables::of($query)->make(true);
    }
}
