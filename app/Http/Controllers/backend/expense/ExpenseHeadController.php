<?php

namespace App\Http\Controllers\backend\expense; 

use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ExpenseHead;
use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ExpenseHeadController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Expense Heads'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.expenses.expense-heads.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['item'] = ExpenseHead::find($id);
            $data['title'] = 'Edit';
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        $data['expenseCategories'] = ExpenseCategory::where('status',1)->get()->toArray();
        return view('backend.expenses.expense-heads.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by_id'] = Auth::guard('admin')->user()->id;
        ExpenseHead::create($data);
        return redirect()->route('expense-heads.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $expenseHead = ExpenseHead::find($id);
        $data['updated_by_id'] = Auth::guard('admin')->user()->id;
        $expenseHead->update($data);
        return redirect()->route('expense-heads.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }

    public function list(Request $request)
    {
        $query = ExpenseHead::join('expense_categories', 'expense_categories.id', '=', 'expense_heads.expense_category_id');
        if(!$request->has('order')) $query = $query->orderBy('id','desc');
        $query = $query->select(['expense_heads.*', 'expense_categories.cat_name']);
        return DataTables::of($query)->make(true);
    }
}
