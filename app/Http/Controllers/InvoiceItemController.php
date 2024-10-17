<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Inventory;
use App\Models\Business;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;



class InvoiceItemController extends Controller
{
    public function index(){
        return InvoiceItem::all();
    }


    public function show($invoice_system_id)
    {
        $invoice = InvoiceItem::where('invoice_system_id', $invoice_system_id)->get();
    
        if (!$invoice) {
            return response()->json(['error' => 'Invoice Items not found'], 404);
        }
    
        return response()->json($invoice);
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'invoice_system_id' => 'nullable|exists:invoices,invoice_system_id',

                'product_id' => 'nullable|exists:products,id',
                'product_name' => 'nullable|string|max:255',
                'product_price' => 'nullable|numeric',
            
                'on_sale' => 'nullable|in:yes,no',
                'on_sale_price' => 'nullable' ,

                'seniorPWD_discountable' => 'nullable|in:yes,no',

                'final price' => 'nullable|numeric',

                'quantity' => 'nullable|numeric',
                'amount' => 'nullable|numeric'
            ]);
            

            $invoice_item = InvoiceItem::create([
                'invoice_system_id' => $request->invoice_system_id,
                
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'product_price' => $request->product_price,

                'on_sale' => $request->on_sale,
                'on_sale_price' => $request->on_sale_price,
                'seniorPWD_discountable' => $request->seniorPWD_discountable,
                    'final_price' => $request->final_price,

                'quantity' => $request->quantity,
                'amount' => $request->amount,

            ]);




        }catch(Exception $e){
            return response()->json(data:['error in generating invoice items' => $e->getMessage()], status: 500);
        }
    }

}
