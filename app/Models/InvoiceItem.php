<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        //'business_id','business_Address','business_TIN',
        
        'invoice_system_id',
        
        'product_id',
        'product_name',
        'product_price',
        
        'on_sale',
        'on_sale_price',
        'seniorPWD_discountable',
            'final_price',
        
        'quantity',
        'amount',

        'timestamps'
    ];
}
