<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'created_by',
        'role'
    ];
    
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
