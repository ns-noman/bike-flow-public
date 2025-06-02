<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentUpload extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'document_type_id',
        'bike_purchase_id',
        'bike_sale_id',
        'document_name',
    ];
}
