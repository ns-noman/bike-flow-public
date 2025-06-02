<?php

namespace App\Http\Controllers\backend\expense; 

use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ExpenseDetails;
use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;


class ExpenseCategoryController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Expense Categories'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.expenses.expense-categories.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['expense_category'] = ExpenseCategory::find($id);
            $data['title'] = 'Edit';
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.expenses.expense-categories.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by_id'] = Auth::guard('admin')->user()->id;
        ExpenseCategory::create($data);
        return redirect()->route('expense-categories.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $expenseHead = ExpenseCategory::find($id);
        $data['updated_by_id'] = Auth::guard('admin')->user()->id;
        $expenseHead->update($data);
        return redirect()->route('expense-categories.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    public function list(Request $request)
    {
        $query = ExpenseCategory::query();
        if(!$request->has('order')) $query = $query->orderBy('id','desc');
        return DataTables::of($query)->make(true);
    }
}

