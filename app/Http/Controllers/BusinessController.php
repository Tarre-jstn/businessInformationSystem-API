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

    $business_image=null;
    if ($request->hasFile('business_image')) {
        $image = $request->file('business_image');
        $path = $image->store('public/business_logos');
        $business_image = basename($path);
    }

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
    public function update(Request $request, $id)
    {
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
        
        $business = Business::findOrFail($id);
        $oldData = $business->toArray();
        $oldName = $business->business_Name;
        $oldImage = $business->business_image;

        $changes = [];
        $ignoreImageChange = false; // New flag to ignore specific image changes

        if ($request->hasFile('business_image')) {
            $image = $request->file('business_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Move the old image to the archive if it exists, regardless of the replacement image
            if ($oldImage && $oldImage !== 'default_logo.png' && $oldImage !== null) {
                $oldImagePath = 'public/business_logos/' . $oldImage;
                if (Storage::exists($oldImagePath)) {
                    Storage::move($oldImagePath, 'public/business_logos/archive/' . $oldImage);
                }
            }

            // Store the new image
            $image->storeAs('public/business_logos', $imageName);
            $business->business_image = $imageName;

            // Track the change only if the old image was not default or null
            if ($oldImage !== 'default_logo.png' && $oldImage !== null) {
                $changes['business_image'] = ['old' => $oldImage, 'new' => $imageName];
            } else {
                $ignoreImageChange = true;
            }
        }

        $oldAddress = "{$oldData['business_Address']}, {$oldData['business_Barangay']}, {$oldData['business_City']}, {$oldData['business_Province']}";
        $newAddress = "{$request->input('business_Address')}, {$request->input('business_Barangay')}, {$request->input('business_City')}, {$request->input('business_Province')}";

        if ($oldAddress !== $newAddress) {
            $changes['business_Address'] = ['old' => $oldAddress, 'new' => $newAddress];
            // Update address fields in the model
            $business->business_Address = $request->input('business_Address');
            $business->business_Barangay = $request->input('business_Barangay');
            $business->business_City = $request->input('business_City');
            $business->business_Province = $request->input('business_Province');
        }

        if ($oldName !== $request->input('business_Name')) {
            $changes['business_Name'] = ['old' => $oldName, 'new' => $request->input('business_Name')];
            $business->business_Name = $request->input('business_Name');
        }

        // Similar comparison for address and other fields...
        $fieldsToCheck = [
            'business_Phone_Number', 'business_Telephone_Number', 'business_Email',
            'business_Facebook', 'business_X', 'business_Instagram', 'business_Tiktok',
        ];

        foreach ($fieldsToCheck as $field) {
            if ($oldData[$field] !== $request->input($field)) {
                $changes[$field] = ['old' => $oldData[$field], 'new' => $request->input($field)];
                $business->{$field} = $request->input($field);
            }
        }

        if (!empty($changes)) {
            Log::info('Event dispatching...');
            event(new BusinessNameUpdated($business, $oldName, $business->business_Name, $changes, $oldImage, $ignoreImageChange));
        } else {
            Log::info('No changes detected, event not triggered');
        }

        $business->save();
        return response()->json(['success' => true]);
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
