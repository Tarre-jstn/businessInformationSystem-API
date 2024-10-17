<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Business;
use App\Models\Invoice_Item;
use App\Models\InvoiceAdditional;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class InvoiceAdditionalController extends Controller
{
    //
    public function index()
    {
        return InvoiceAdditional::all();
    }


    public function show($invoice_system_id)
    {
        $invoice = InvoiceAdditional::where('invoice_system_id', $invoice_system_id)->get();
    
        if (!$invoice) {
            return response()->json(['error' => 'Invoice Additionals not found'], 404);
        }
    
        return response()->json($invoice);
    }


    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'invoice_system_id' => 'nullable|exists:invoices,invoice_system_id',
                'addtl_Costs_Description' => 'nullable|string|max:1000',
                'aCD_quantity' => 'nullable|string|max:1000',
                'aCD_Total_Amount' => 'nullable|string|numeric',
                'aCD_amount' => 'nullable|numeric',
            ]);

            $invoice_additional = InvoiceAdditional::create([
                'invoice_system_id' => $request->invoice_system_id,
                'aCD_quantity' => $request->aCD_quantity,
                'addtl_Costs_Description' => $request->addtl_Costs_Description,
                'aCD_amount' => $request->aCD_amount,
                'aCD_Total_Amount' => $request->aCD_Total_Amount
            ]);

        }catch (Exception $e){
            return response()->json(['error in generating invoice additional' => $e->getMessage()], 500);
        }
    }
}
