<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDetails extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'expense_id',
        'expense_head_id',
        'amount',
        'quantity',
        'note',
    ];
    
    public function expense_head()
    {
        return $this->belongsTo(ExpenseHead::class, 'expense_head_id')->select('id','title');
    }
    public function expense_cat()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_cat_id')->select('id','cat_name');
    }
}
