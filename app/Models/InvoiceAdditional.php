<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAdditional extends Model
{
    use HasFactory;
    protected $fillable = [
        //'business_id','business_Address','business_TIN',        
        'invoice_system_id',
        'addtl_Costs_Description',
        'aCD_quantity',
        'aCD_amount',
        'aCD_Total_Amount'

    ];
}
