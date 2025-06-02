<?php

namespace App\Http\Controllers\backend\expense; 

use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseDetails;
use App\Models\ExpenseHead;
use App\Models\BasicInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

class ExpenseController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Expenses'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.expenses.expenses.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Expense::find($id);
            $data['expense_detals'] = ExpenseDetails::with(['expense_head', 'expense_cat'])->where('expense_id', $id)->get();
        }else{
            $data['title'] = 'Create';  
        }
        $data['expenseheads'] = ExpenseHead::where('status',1)->get();
        $data['expense_categories'] = ExpenseCategory::where('status',1)->get();
        $data['breadcrumb'] = $this->breadcrumb;
        $data['paymentMethods'] = $this->paymentMethods();
        return view('backend.expenses.expenses.create-or-edit',compact('data'));
    }
    public function view($id)
    {
        $data['basicInfo'] = BasicInfo::first()->toArray();
        $data['master'] = Expense::join('admins', 'admins.id', '=', 'expenses.created_by_id')
                            ->join('accounts', 'accounts.id', '=', 'expenses.account_id')
                            ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id')
                            ->select([
                                'expenses.*',
                                'admins.name as created_by',
                                'payment_methods.name as payment_method'
                            ])
                            ->where('expenses.id', $id)
                            ->first()->toArray();
        $data['master']['details'] = ExpenseDetails::join('expense_heads', 'expense_heads.id', '=', 'expense_details.expense_head_id')
                                    ->where('expense_id', $id)
                                    ->select('expense_details.*', 'expense_heads.title as expense_head')
                                    ->get()
                                    ->toArray();
        return view('backend.expenses.expenses.view',compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['total_amount'] = $request->total_amount;
        $data['expense_no'] = $this->formatNumber(Expense::max('expense_no')+1);
        $expense = Expense::create($data);
        for($i = 0; $i < count($data['expense_head_id']); $i++) {
           $expenseDetails = 
            [
                'expense_id'=> $expense->id,
                'expense_head_id'=> $data['expense_head_id'][$i],
                'amount'=> $data['amount'][$i],
                'quantity'=> $data['quantity'][$i],
                'note'=> $data['note_'][$i],
            ];
            ExpenseDetails::create($expenseDetails);
        }
        return redirect()->route('expenses.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }

    public function update(Request $request, $id)
    {
        // Expense
        $total_expense = 0;
        $expense = Expense::find($id);
        for($i = 0; $i < count($request->expense_head_id); $i++) $total_expense += $request->amount[$i] * $request->quantity[$i];
        $data = $request->all();
        $data['updated_by_id'] = Auth::guard('admin')->user()->id;
        $expense->update($data);
        ExpenseDetails::where('expense_id',$id)->delete();
        for($i = 0; $i < count($data['expense_head_id']); $i++) {
           $expenseDetails = 
            [
                'expense_id'=> $expense->id,
                'expense_head_id'=> $data['expense_head_id'][$i],
                'amount'=> $data['amount'][$i],
                'quantity'=> $data['quantity'][$i],
                'note'=> $data['note_'][$i],
            ];
            ExpenseDetails::create($expenseDetails);
        }
        return redirect()->route('expenses.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    public function details(Request $request)
    {
        $expense = Expense::with(['expense_details'])->find($request->expense_id);
        return response()->json($expense, 200);
    }
    public function destroy($id)
    {
        $expense = Expense::find($id);
        ExpenseDetails::where('expense_id',$expense->id)->delete();
        $expense->delete();
        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }
    public function reports(Request $request)
    {
        if($request->isMethod('post')){
            $expense_cat_id = $request->expense_cat_id;
            $expense_head_id = $request->expense_head_id;
            $date = $request->date;

            $exp = Expense::join('expense_details', 'expense_details.expense_id', '=', 'expenses.id')
                                    ->join('expense_categories', 'expense_details.expense_cat_id', '=', 'expense_categories.id')
                                    ->join('expense_heads', 'expense_details.expense_head_id', '=', 'expense_heads.id');
                                    
                if ($date) $exp = $exp->where('expenses.date', 'like', "%$date%");
                if($expense_cat_id==-1 || $expense_head_id == -1){
                    if ($expense_cat_id==-1) $exp = $exp->groupBy('expense_details.expense_cat_id');
                    if ($expense_head_id==-1) $exp = $exp->groupBy('expense_details.expense_head_id');
                    $exp = $exp->orderBy('expenses.date', 'asc')
                    ->select(DB::raw('sum(amount*quantity) as sub_total'), 'expense_categories.cat_name', 'expense_heads.title')
                    ->get();
                }else{
                    if ($expense_cat_id>0) $exp = $exp->where('expense_details.expense_cat_id', $expense_cat_id);
                    if ($expense_head_id>0) $exp = $exp->where('expense_details.expense_head_id', $expense_head_id);
                    $exp = $exp->orderBy('expenses.date', 'asc')
                            ->select('expenses.date', 'expense_details.*',DB::raw('expense_details.amount * expense_details.quantity as sub_total'), 'expense_categories.cat_name', 'expense_heads.title')
                            ->get();
                }
                

            
            $data['expenses'] = $exp;

            $data['currency_symbol'] = $data['currency_symbol'] = BasicInfo::first()->currency_symbol;
            return response()->json($data, 200);
        }else{
            $data['expense_heads'] = ExpenseHead::where('status', 1)->get();
            $data['expense_categories'] = ExpenseCategory::where('status',1)->get();
            return view('backend.expenses.reports.index', compact('data'));
        }
    }
    public function list(Request $request)
    {
        $totalExpense = Expense::join('admins', 'admins.id', '=', 'expenses.created_by_id')
                        ->sum('expenses.total_amount');

        $query = Expense::join('admins', 'admins.id', '=', 'expenses.created_by_id')
                        ->join('accounts', 'accounts.id', '=', 'expenses.account_id')
                        ->join('payment_methods', 'payment_methods.id', '=', 'accounts.payment_method_id');
        if(!$request->has('order')) $query = $query->orderBy('expenses.id','desc');
        $query = $query->select(['expenses.*', 'admins.name as created_by', 'payment_methods.name as payment_method']);
        return DataTables::of($query)->with(['totalExpense'=>$totalExpense])->make(true);
    }
    
    public function expenseHead($expense_category_id)
    {
        $data = ExpenseHead::where(['status'=>1, 'expense_category_id'=> $expense_category_id])->select('id','title')->get()->toArray();
        return response()->json($data, 200);
    }

    public function approve($id)
    {
        DB::beginTransaction();
        
        try {
            $expense = Expense::findOrFail($id);
            $expense->update(['status' => 1]);
    
            $this->accountTransaction([
                'account_id'        => $expense->account_id,
                'debit_amount'      => $expense->total_amount,
                'reference_number'  => $expense->reference_number,
                'description'       => 'Expenses',
                'transaction_date'  => $expense->date,
            ]);

            $investorLedger = [
                'investor_id'       => 1,
                'account_id'        => $expense->account_id,
                'debit_amount'      => $expense->total_amount,
                'transaction_date'  => $expense->date,
                'particular'       => "Expenses",
                'reference_number'  => $expense->reference_number,
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
                'message' => 'Error approving purchase: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
}
