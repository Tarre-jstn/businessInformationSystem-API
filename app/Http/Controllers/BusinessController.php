<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try{
    $request->validate([
        'business_id' => 'required|exists:businesses,business_id',
        'business_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'user_id' => 'required|numeric|exists:users,id',
        'business_Name' => 'required|string|max:255',
        'business_Address' => 'required|string|max:255',
        'business_Contact_Number' => 'required|string|max:255',
        'business_Email'=> 'required|string|lowercase|email|max:255|unique:businesses,business_Email',
        'business_Facebook'=>'nullable|url',
        'business_X'=>'nullable|url',
        'business_Instagram'=>'nullable|url',
        'business_Tiktok'=>'nullable|url'
        'business_TIN'=> 'required|numeric',
        'business_Facebook'=>'required|string|max:255',
        'business_X'=>'required|string|max:255',
        'business_Instagram'=>'required|string|max:255',
        'business_Tiktok'=>'required|string|max:255'

    ]);

    $business_image=null;
    if ($request->hasFile('business_image')) {
        $image = $request->file('business_image');
        $path = $image->store('public/business_logos');
        $business_image = basename($path);
    }

    if($request->user_id){
        $user = User::find($request->user_id);
        if($user->user_type=='owner'){
            $business = Business::create([
            'business_image' => $business_image,
            'user_id' => $request->user_id,
            'business_Name' => $request->business_Name,
            'business_Address' => $request->business_Address,
            'business_Contact_Number' => $request->business_Contact_Number,
            'business_Email'=> $request->business_Email,
            'business_Facebook'=>$request->business_Facebook,
            'business_X'=> $request->business_X,
            'business_Instagram'=> $request->business_Instagram,
            'business_Tiktok'=> $request->business_Tiktok
    ]);
}
    }else{
        throw new \Exception('The selected user must be an owner.');
    }
    }catch(Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



public function showBusiness(Request $request){
    $user_id = $request->query('user_id');
    $business = Business::where('user_id', $user_id)->first();
    if (!$business) {
        return response()->json(['error' => 'Business record not found'], 404);
    }
    return response()->json($business);
}

}
