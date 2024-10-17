<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Business;
use App\Models\InvoiceItem;
use App\Models\InvoiceAdditional;
use App\Models\InvoiceComputation;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::all();
    }


public function show($invoice_id)
{
    $invoice = Invoice::where('invoice_id', $invoice_id)->first();

    if (!$invoice) {
        return response()->json(['error' => 'Invoice not found'], 404);
    }

    return response()->json($invoice);
}

    
    public function store(Request $request)
    {
        try{
            $request->validate([

                //FOR BUSINESS INFORMATION
                    'business_id' => 'nullable|exists:businesses,business_id',

                    'business_Name' => 'required|string|max:255',
                    'business_Address' => 'required|string|max:255',
                    'business_TIN' => 'required|numeric',
        
                //FOR INVOICE
                    'invoice_id' => 'required|integer|unique:invoices,invoice_id',
                    'date' => 'required|date',
                    'terms' => 'required|string|max:255',
                    'status' => 'required|string|max:255',
                    'authorized_Representative' => 'required|string|max:255',
                    'payment_Type' => 'required|string|max:255', 
        
                //FOR CUSTOMER INFO IN INVOICE
                    'customer_Name' => 'required|string|max:255',
                    'customer_Address' => 'required|string|max:255',
                    'customer_TIN' => 'required|integer',
                    'customer_Business_Style' => 'required|string|max:255',
                    'customer_PO_No' => 'required|integer',
                    'customer_OSCA_PWD_ID_No' => 'required|integer',
        
                ]);
                
                
                $businessId = $request->input('business_id');
                $business = Business::where('business_id', $businessId)->first();
        
                if (!$business) {
                    // Use the first available business record if the provided one is invalid
                    $business = Business::first();
                    if (!$business) { 
                        return response()->json(['error' => 'No business record found'], 404);
                    }
                }
                // $business = Business::where('business_id', $request->business_id)->first(); //CHECKS IF THERE'S A BUSINESS ID
                



                if($business){
                        $invoice = Invoice::create([
                        'business_id' => $business->business_id,
                        'business_Name' => $business->business_Name,  // Use $business directly
                        'business_Address' => $business->business_Address,
                        'business_TIN' => $business->business_TIN,

                        'invoice_id' => $request->invoice_id,
                        'date' => $request->date,
                        'terms' => $request->terms,
                        'status' => $request->status,
                        'authorized_Representative' => $request->authorized_Representative,
                        'payment_Type' => $request->payment_Type,

                        'customer_Name' => $request->customer_Name,
                        'customer_Address' => $request->customer_Address,
                        'customer_TIN' => $request->customer_TIN,
                        'customer_Business_Style' => $request->customer_Business_Style,
                        'customer_PO_No' => $request->customer_PO_No,
                        'customer_OSCA_PWD_ID_No' => $request->customer_OSCA_PWD_ID_No,


                ]);
            
                return response()->json([
                    'message' => 'Invoice created successfully',
                    'invoice_system_id' => $invoice->invoice_system_id // Pass this to the next request
                ], 201);
            
            }else {
                return response()->json(['error' => 'Business not found'], 404);
            }
        }
        catch(Exception $e){
            return response()->json(['error in generating invoice' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $invoice_system_id) {
        // Find the invoice by its invoice_system_id
        $invoice = Invoice::where('invoice_system_id', $invoice_system_id)->firstOrFail();

        // Validate the request data
        $request->validate([
            'invoice_id' => [
                'nullable',
                Rule::unique('invoices', 'invoice_id')->ignore($invoice->invoice_system_id, 'invoice_system_id')     // Correctly use the Rule class
            ],
            'date' => 'required|date',
            'terms' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'authorized_Representative' => 'required|string|max:255',
            'payment_Type' => 'required|string|max:255',
            'customer_Name' => 'required|string|max:255',
            'customer_Address' => 'required|string|max:255',
            'customer_TIN' => 'required|integer',
            'customer_Business_Style' => 'required|string|max:255',
            'customer_PO_No' => 'required|integer',
            'customer_OSCA_PWD_ID_No' => 'required|integer',

        ]);
    
        // Update the invoice with the new data
        if($invoice){
            $data = $request->all();
            $invoice->update($data);
            $invoice->save();
    
    
    
            return response()->json($invoice);
        }
    }

    public function destroy($invoice_system_id)
    {
        try {
            // Find the invoice by invoice_system_id
            $invoice = Invoice::where('invoice_system_id', $invoice_system_id)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }
    
            // Delete the invoice
            $invoice->delete();
    
            return response()->json(['message' => 'Invoice deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unable to delete invoice: ' . $e->getMessage()], 500);
        }
    }
    public function invoice_print($invoice_id)
    {
        // Fetch the invoice based on invoice_id without loading any related models
        $invoice = Invoice::where('invoice_id', $invoice_id)->first();
        // $invoice = Invoice::where('invoice_id', $invoice_id)->first()::with('business')->find($invoice_id);
        $invoice_items = InvoiceItem::where('invoice_system_id', $invoice->invoice_system_id)->get();
        $invoice_additionals = InvoiceAdditional::where('invoice_system_id',$invoice->invoice_system_id)->get();
        $invoice_computation = InvoiceComputation::where('invoice_system_id',$invoice->invoice_system_id)->first();
        // $invoice_item = DB::table(invoice_items)->where(,);

        // Check if the invoice exists
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
        if (!$invoice_items) {
             return response()->json(['error' => 'Invoice Items not found'], 404);
        }
        if ($invoice_items->isEmpty()) {
            // Log the issue or handle it accordingly
            \Log::info('No invoice items found for invoice_system_id: ' . $invoice->invoice_system_id);
        }
        if (!$invoice_additionals) {
            return response()->json(['error' => 'Invoice Items not found'], 404);
       }
        if ($invoice_additionals->isEmpty()) {
            // Log the issue or handle it accordingly
            \Log::info('No invoice items found for invoice_system_id: ' . $invoice->invoice_system_id);
        }if (!$invoice_computation) {
            return response()->json(['error' => 'Invoice Computation not found'], 404);
       }

        // Pass the fetched invoice data directly to the PDF view
        $pdf = Pdf::loadView('invoicepdf', ['invoice' => $invoice, 'invoice_items' => $invoice_items, 'invoice_additionals' => $invoice_additionals, 'invoice_computation' => $invoice_computation]);
    
        // Stream the generated PDF to the browser
        return $pdf->stream();
    }
}

