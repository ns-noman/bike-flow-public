<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Investor;
use App\Models\AccountLedger;
use App\Models\PaymentMethod;
use App\Models\InvestorLedger;
use App\Models\SupplierLedger;
use App\Models\Supplier;
use App\Models\CustomerLedger;
use App\Models\Customer;
use App\Models\PartyLedger;
use App\Models\Party;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function getUserInfo()
    {
        return Auth::guard('admin')->user();
    }
    public function getUserId()
    {
        return Auth::guard('admin')->user()->id;
    }
    public function paymentMethods()
    {
        return PaymentMethod::join('accounts', 'accounts.payment_method_id', '=','payment_methods.id')
        ->where(['is_virtual'=>0, 'payment_methods.status'=>1, 'accounts.status'=>1])
        ->select([
            'accounts.id',
            'payment_methods.name',
            'accounts.account_no',
            'accounts.balance',
        ])
        ->get()->toArray();
    }


    public function investorLedger($data)
    {
        try {
            DB::beginTransaction();
            $data['credit_amount'] = $data['credit_amount'] ?? null;
            $data['debit_amount'] = $data['debit_amount'] ?? null;
            $data['particular'] = $data['particular'] ?? null;
            $data['transaction_type'] = $data['transaction_type'] ?? null;
            $currentBalance = InvestorLedger::where(['investor_id'=>$data['investor_id']])->orderBy('id','desc')->pluck('current_balance')->first() ?? 0;
            $newcurrentBalance = $currentBalance + $data['credit_amount'] - $data['debit_amount'];
            $data['current_balance'] = $newcurrentBalance;
            InvestorLedger::create($data);
            Investor::find($data['investor_id'])->update(['balance'=>$newcurrentBalance]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function supplierLedgerTransction($data)
    {
        DB::beginTransaction();
        try {
            $currentBalance = SupplierLedger::where('supplier_id', $data['supplier_id'])
                                ->orderBy('id', 'desc')
                                ->first()->current_balance ?? 0;
            $data['supplier_id'] = $data['supplier_id'];
            $data['purchase_id'] = $data['purchase_id'] ?? null;
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
            $data['current_balance'] = $currentBalance + $data['debit_amount'] - $data['credit_amount'];
            SupplierLedger::create($data);
            Supplier::find($data['supplier_id'])->update(['current_balance'=> $data['current_balance']]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function customerLedgerTransction($data)
    {
        DB::beginTransaction();
        try {
            $currentBalance = CustomerLedger::where('customer_id', $data['customer_id'])
                                ->orderBy('id', 'desc')
                                ->first()->current_balance ?? 0;
            $data['customer_id'] = $data['customer_id'];
            $data['sale_id'] = $data['sale_id'] ?? null;
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
            CustomerLedger::create($data);
            Customer::find($data['customer_id'])->update(['current_balance'=> $data['current_balance']]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function accountTransaction($data)
    {
        try {
            DB::beginTransaction(); // Start Transaction
    
            // Ensure credit and debit amounts are set
            $data['credit_amount'] = $data['credit_amount'] ?? null;
            $data['debit_amount'] = $data['debit_amount'] ?? null;
    
            // Get latest account balance (or default to 0)
            $currentAccountBalance = AccountLedger::where('account_id', $data['account_id'])
                ->latest()
                ->pluck('current_balance')
                ->first() ?? 0;
    
            // Calculate new balance
            $data['current_balance'] = $currentAccountBalance + $data['credit_amount'] - $data['debit_amount'];
    
            // Insert new ledger entry
            AccountLedger::create($data);
    
            // Update Account balance
            $account = Account::find($data['account_id']);
            if (!$account) {
                throw new \Exception("Account not found.");
            }
    
            $account->updateOrFail(['balance' => $data['current_balance']]);
    
            DB::commit(); // Commit Transaction (Apply Changes)
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction (Undo Changes)
            throw $e; // Re-throw the error for handling in the calling function
        }
    }
    
    public function partyLedgerTransction($data)
    {
        DB::beginTransaction();
        try {
            $currentBalance = PartyLedger::where('party_id', $data['party_id'])->orderBy('id', 'desc')->first()->current_balance ?? 0;
            $data['party_id'] = $data['party_id'];
            $data['loan_id'] = $data['loan_id'] ?? null;
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
    public function formatNumber($number)
    {
        return str_pad($number, 7, '0', STR_PAD_LEFT);
    }

}
