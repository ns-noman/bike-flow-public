<?php

namespace App\Http\Controllers\backend\loans;

use App\Models\Party;
use App\Models\BasicInfo;
use App\Models\PartyLedger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class PartyController extends Controller
{
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Parties'];}
    public function index()
    {
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.loans.parties.index', compact('data'));
    }

    public function createOrEdit($id=null)
    {
        if($id){
            $data['title'] = 'Edit';
            $data['item'] = Party::find($id);
        }else{
            $data['title'] = 'Create';
        }
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.loans.parties.create-or-edit',compact('data'));
    }

    public function store(Request $request)
    {
        //Party Create**********
        $data = $request->all();
        $data['created_by_id'] = Auth::guard('admin')->user()->id;
        $party = Party::create($data);
        return redirect()->route('parties.index')->with('alert',['messageType'=>'success','message'=>'Data Inserted Successfully!']);
    }


    public function update(Request $request, $id)
    {
        $party = Party::find($id);
        $data = $request->all();
        $data['update_by_id'] = Auth::guard('admin')->user()->id;
        $party->update($data);
        return redirect()->route('parties.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);
    }
    
    public function destroy($id)
    {
        $party = Party::find($id);
        if(count($data)) return redirect()->back()->with('alert',['messageType'=>'warning','message'=>'Data Deletion Failed!']);
        $party->delete();
        return redirect()->back()->with('alert',['messageType'=>'success','message'=>'Data Deleted Successfully!']);
    }

    public function partyLedgerTransction($data)
    {
        DB::beginTransaction();
        try {
            $currentBalance = PartyLedger::where('party_id', $data['party_id'])
                                ->orderBy('id', 'desc')
                                ->first()->current_balance ?? 0;

            $data['party_id'] = $data['party_id'];
            $data['loan_id'] = $data['loan_id'] ?? null;
            $data['loan_type'] = $data['loan_type'];
            $data['payment_id'] = $data['payment_id'] ?? null;
            $data['account_id'] = $data['account_id'] ?? null;
            $data['particular'] = $data['particular'] ?? null;
            $data['date'] = $data['date'] ?? null;
            $data['debit_amount'] = $data['debit_amount'] ?? null;
            $data['credit_amount'] = $data['credit_amount'] ?? null;
            $data['reference_number'] = $data['reference_number'] ?? null;
            $data['note'] = $data['note'] ?? null;
            $data['created_by_id'] = $data['created_by_id'] ?? null;
            $data['updated_by_id'] = $data['updated_by_id'] ?? null;
            $data['current_balance'] = $currentBalance - $data['debit_amount'] + $data['credit_amount'];
            PartyLedger::create($data);
            Party::find($data['party_id'])->update(['current_balance'=> $data['current_balance']]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function list(Request $request)
    {
        $query = Party::query();
                    if(!$request->has('order')) $query = $query->orderBy('id','desc');
        return DataTables::of($query)->make(true);
    }
}