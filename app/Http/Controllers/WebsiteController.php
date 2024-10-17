<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Website;
use Exception;
use Illuminate\Support\Facades\Log;


class WebsiteController extends Controller
{
    public function index()
    {
        Website::all();
    }
    
    public function store (Request $request){
        try{
            $request->validate([
                'business_id' => 'required|numeric|exists:businesses,business_id',
                'website_description'=>'nullable|string|max:255',
                'website_details' => 'nullable|string|max:255',
                'website_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'about_us1'=>'nullable|string|max:255',
                'about_us2'=>'nullable|string|max:255',
                'about_us3'=>'nullable|string|max:255'
            ]);
    
            $website=Website::create($request->all());
            return response()->json($website, 201);
            }
            catch(Exception $e){
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    

    public function info(Request $request){
        $business_id = $request->query('business_id');
        $website = Website::where('business_id', $business_id)->first();
        return response()->json($website);
    }

    public function update(Request $request){
       
        
        $request->validate([
            'website_description'=>'nullable|string|max:255',
            'website_details' => 'nullable|string|max:255',
            'website_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about_us1'=>'nullable|string|max:255',
            'about_us2'=>'nullable|string|max:255',
            'about_us3'=>'nullable|string|max:255',
            'featured_section'=>'required|in:true,false',
            'onSale_section'=>'required|in:true,false'
        ]);

        $website = Website::where('business_id', $request->business_id)->first();
        
        if($website){
        $data = $request->all();
        $website->update($data);
        if($request->hasFile('website_image')){
            $image = $request->file('website_image');
            $path = $image->store('images','public');
            $website->website_image = $path;
            $website->save();
            Log::info('Received the file!');
        }

        $website->save();

        return response()->json(['message' => 'Website updated successfully'], 200);
        }
    }

}
