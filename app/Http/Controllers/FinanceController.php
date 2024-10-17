<?php

namespace App\Http\Controllers;
use App\Models\Finance;
use App\Models\FinanceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\carbon;
class FinanceController extends Controller
{
    public function index()
    {
        $financeCategories = FinanceCategory::all();
        $finances = Finance::all();
    
        return response()->json([
            'financeCategories' => $financeCategories,
            'finances' => $finances
        ]);
    }


    public function show(){

    }
    
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',

        ]);

        $finance = new Finance($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $finance->image = $path;
        }

        $finance->save();

        return response()->json(['message' => 'Finance added successfully', 'finance' => $finance], 201);
    }

    public function update(Request $request, $id)
    {

        \Log::info('Incoming request data:', $request->all());
        
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        $finance = Finance::findOrFail($id);

        $finance->update($validated);

        return response()->json(['finance' => $finance], 200);
    }

    public function destroy($id)
    {
        try {
            $finance = Finance::findOrFail($id);
            $finance->delete();

            return response()->json(['message' => 'Finance deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting finance: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete product'], 500);
        }
    }


    public function storeCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255'
        ]);

        $finance = new FinanceCategory($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $finance->image = $path;
        }

        $finance->save();

        return response()->json(['message' => 'Finance added successfully', 'finance' => $finance], 201);
    }


    public function destroyCategory($id)
    {
        try {
            $finance = FinanceCategory::findOrFail($id);
            $finance->delete();

            return response()->json(['message' => 'Finance Category deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting finance: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete product'], 500);
        }
    }

    public function getFinanceByDate(Request $request)
    {
        \Log::info('Incoming request data FOR FINANCE BY DATE:', $request->all());

        if (!$request->has(['start_date', 'end_date'])) {
            return response()->json(['error' => 'Start date and end date are required'], 400);
        }

        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
    
        $financesByDate = Finance::whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date')
                        ->get();
    
        return response()->json($financesByDate);
    }

}

