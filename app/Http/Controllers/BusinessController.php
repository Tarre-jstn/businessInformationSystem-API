<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Events\BusinessNameUpdated;


class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $Business = Business::where('user_id', auth()->id())->first(); 
    return response()->json($Business);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        $request->validate([
            'business_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_Name' => 'required|string|max:255',
            'business_Email' => 'required|string|lowercase|email|max:255',
            'business_Province' => 'required|string|max:255',
            'business_City' => 'required|string|max:255',
            'business_Barangay' => 'required|string|max:255',
            'business_Address' => 'required|string|max:255',
            'business_Phone_Number' => 'required|string|max:255',
            'business_Telephone_Number' => 'required|string|max:255',
            'business_Facebook' => 'nullable|string|max:255',
            'business_X' => 'nullable|string|max:255',
            'business_Instagram' => 'nullable|string|max:255',
            'business_Tiktok' => 'nullable|string|max:255'
        ]);

        $business_image = null; // Initialize the variable

        if ($request->hasFile('business_image')) {
            $image = $request->file('business_image');
            // Store the image in the 'public/business_logos' directory
            $path = $image->store('public/business_logos');
            // Get the basename of the stored file path
            $business_image = basename($path);
        }

        // Ensure the user is of type 'owner' before creating the business
        if ($request->user_id) {
            $user = User::find($request->user_id);
            if ($user->user_type == 'owner') {
                $Business = Business::create([
                    'business_image' => $business_image,  // Save the image path here
                    'business_Name' => $request->business_Name,
                    'business_Email' => $request->business_Email,
                    'business_Province' => $request->business_Province,
                    'business_City' => $request->business_City,
                    'business_Barangay' => $request->business_Barangay,
                    'business_Address' => $request->business_Address,
                    'business_Phone_Number' => $request->business_Phone_Number,
                    'business_Telephone_Number' => $request->business_Telephone_Number,
                    'business_Facebook' => $request->business_Facebook,
                    'business_X' => $request->business_X,
                    'business_Instagram' => $request->business_Instagram,
                    'business_Tiktok' => $request->business_Tiktok
                ]);

                return response()->json(['success' => true, 'business' => $Business], 201);
            }
        } else {
            throw new \Exception('The selected user must be an owner.');
        }
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Business $Business)
    {
        return $Business->all();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, Request $request, Business $Business)
    {
        $request->validate([
            'business_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_Name' => 'required|string|max:255',
            'business_Email'=> 'required|string|lowercase|email|max:255', 
            'business_Province' => 'required|string|max:255',
            'business_City' => 'required|string|max:255',
            'business_Barangay' => 'required|string|max:255',
            'business_Address' => 'required|string|max:255',
            'business_Phone_Number' => 'required|string|max:255',
            'business_Telephone_Number' => 'required|string|max:255',
            'business_Facebook'=>'nullable|string|max:255',
            'business_X'=>'nullable|string|max:255',
            'business_Instagram'=>'nullable|string|max:255',
            'business_Tiktok'=>'nullable|string|max:255'
        ]);

        $business = Business::findOrFail($id);
        $oldData = $business->toArray(); // Store the original data
        $oldName = $business->business_Name;
    
        // Update the business data
        $business->update($request->all());
    
        // Collect changes to track
        $changes = [];
        if ($oldName !== $request->input('business_Name')) {
            $changes['business_Name'] = ['old' => $oldName, 'new' => $request->input('business_Name')];
        }
    
        // Check for address changes
        $oldAddress = "{$oldData['business_Address']}, {$oldData['business_Barangay']}, {$oldData['business_City']}, {$oldData['business_Province']}";
        $newAddress = "{$business->business_Address}, {$business->business_Barangay}, {$business->business_City}, {$business->business_Province}";
    
        if ($oldAddress !== $newAddress) {
            $changes['business_Address'] = ['old' => $oldAddress, 'new' => $newAddress];
        }
    
        // Add other changes (like phone number, email, etc.)
        if ($oldData['business_Phone_Number'] !== $business->business_Phone_Number) {
            $changes['business_Phone_Number'] = ['old' => $oldData['business_Phone_Number'], 'new' => $business->business_Phone_Number];
        }
        if ($oldData['business_Telephone_Number'] !== $business->business_Telephone_Number) {
            $changes['business_Telephone_Number'] = ['old' => $oldData['business_Telephone_Number'], 'new' => $business->business_Telephone_Number];
        }
        if ($oldData['business_Email'] !== $business->business_Email) {
            $changes['business_Email'] = ['old' => $oldData['business_Email'], 'new' => $business->business_Email];
        }
        if ($oldData['business_Facebook'] !== $business->business_Facebook) {
            $changes['business_Facebook'] = ['old' => $oldData['business_Facebook'], 'new' => $business->business_Facebook];
        }
        if ($oldData['business_X'] !== $business->business_X) {
            $changes['business_X'] = ['old' => $oldData['business_X'], 'new' => $business->business_X];
        }
        if ($oldData['business_Instagram'] !== $business->business_Instagram) {
            $changes['business_Instagram'] = ['old' => $oldData['business_Instagram'], 'new' => $business->business_Instagram];
        }
        if ($oldData['business_Tiktok'] !== $business->business_Tiktok) {
            $changes['business_Tiktok'] = ['old' => $oldData['business_Tiktok'], 'new' => $business->business_Tiktok];
        }
    
        // If there are changes, trigger the event
        if (!empty($changes)) {
            event(new BusinessNameUpdated($oldName, $business->business_Name, $changes));
        }
    

        if ($request->hasFile('business_image')) {
            // Handle the file upload
            $image = $request->file('business_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // Save the image to storage/app/public/business_logos
            $image->storeAs('public/business_logos', $imageName);
    
            // Save the image name in the database (just the filename, not the full path)
            $business->business_image = $imageName;
        }

        // Update other business fields
        $business->business_Name = $request->input('business_Name');
        $business->business_Email = $request->input('business_Email');
        $business->business_Province = $request->input('business_Province');
        $business->business_City = $request->input('business_City');
        $business->business_Barangay = $request->input('business_Barangay');
        $business->business_Address = $request->input('business_Address');
        $business->business_Phone_Number = $request->input('business_Phone_Number');
        $business->business_Telephone_Number = $request->input('business_Telephone_Number');
        $business->business_Facebook = $request->input('business_Facebook');
        $business->business_X = $request->input('business_X');
        $business->business_Instagram = $request->input('business_Instagram');
        $business->business_Tiktok = $request->input('business_Tiktok');

        // Save the business profile
        $business->save();

        return response()->json(data: ['success' => true]);
    }

    


    



    /**
     * Remove the specified resource from storage.
     */
        public function destroy(int $id)
    {
        $business = Business::findOrFail($id);
        return [
            'success' => (bool) $business->delete()
        ];
    }



}
