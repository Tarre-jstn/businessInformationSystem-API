<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceComputation;
use App\Models\Inventory;
use App\Models\Business;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;






class InvoiceComputationController extends Controller
{
    //

    public function show($invoice_system_id)
    {
        $invoice = InvoiceComputation::where('invoice_system_id', $invoice_system_id)->first();
    
        if (!$invoice) {
            return response()->json(['error' => 'Invoice Computation not found'], 404);
        }
    
        return response()->json($invoice);
    }
    
    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'invoice_system_id' => 'nullable|exists:invoices,invoice_system_id',

                'VATable_Sales' => 'nullable|numeric',
                'VAT_Exempt_Sales' => 'nullable|numeric',
                'Zero_Rated_Sales' => 'nullable|numeric',
                'VAT_Amount' => 'nullable|numeric',

                'VAT_Inclusive' => 'nullable|numeric',
                'Less_VAT' => 'nullable|numeric',
                'Amount_NET_of_VAT' => 'nullable|numeric',
                'senior_PWD_discountable' => 'nullable|string|max:5',
                'Less_SC_PWD_Discount' => 'nullable|numeric',
                'Less_SC_PWD_Discount_Percent' => 'nullable|numeric',
                'Amount_Due' => 'nullable|numeric',
                'Add_VAT' => 'nullable|numeric',

                'tax' => 'nullable|numeric',
                'total_Amount_Due' => 'nullable|numeric',
            ]);

            $invoice_computation = InvoiceComputation::create([
                'invoice_system_id' => $request->invoice_system_id,
                
                'VATable_Sales' => $request->VATable_Sales,
                'VAT_Exempt_Sales' => $request->VAT_Exempt_Sales,
                'Zero_Rated_Sales' => $request->Zero_Rated_Sales,
                'VAT_Amount' => $request->VAT_Amount,

                'VAT_Inclusive' => $request->VAT_Inclusive,
                'Less_VAT' => $request->Less_VAT,
                'Amount_NET_of_VAT' => $request->Amount_NET_of_VAT,
                'senior_PWD_discountable' => $request->senior_PWD_Discountable,
                'Less_SC_PWD_Discount' => $request->Less_SC_PWD_Discount,
                'Less_SC_PWD_Discount_Percent' => $request->Less_SC_PWD_Discount_Percent,
                'Amount_Due' => $request->Amount_Due,
                'Add_VAT' => $request->Add_VAT,

                'tax' => $request->tax,
                'total_Amount_Due' => $request->total_Amount_Due,

            ]);
            
        }catch (Exception $e){
            return response()->json(['error in generating invoice computation' => $e->getMessage()], 500);
        }

    }

}
