<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceComputation extends Model
{
    use HasFactory;

    protected $fillable = [

        //FOR STORING VAT, DISCOUNTS, ETC. (WITH COMPUTATIONS)
        'invoice_system_id',
        'VATable_Sales',
        'VAT_Exempt_Sales',
        'Zero_Rated_Sales',
        'VAT_Amount',

        'VAT_Inclusive',
        'Less_VAT',
        'Amount_NET_of_VAT',
        'senior_PWD_discountable',
        'Less_SC_PWD_Discount',
        'Less_SC_PWD_Discount_Percent',
        'Amount_Due',
        'Add_VAT',
        
        //FOR STORING TOTAL AMOUNT DUE (FINAL COMPUTATION)
        'tax',
        'total_Amount_Due',

        'timestamps'
    
    ];
}
