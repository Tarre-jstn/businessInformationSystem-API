<?php

namespace App\Http\Controllers;
use App\Models\chatbot_response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chatbot_response = chatbot_response::all();
        return response()->json($chatbot_response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'chabot_BWHours' => 'nullable|string|max:255',
            'chabot_BPDescription' => 'nullable|string|max:255',
            'chabot_Lazada'=> 'nullable|string|max:255',
            'chabot_Shopee'=> 'nullable|string|max:255',
            'chabot_Region1'=> 'nullable|string|max:255',
            'chabot_Region2'=> 'nullable|string|max:255',
            'chabot_Region3'=> 'nullable|string|max:255',
            'chabot_Region4A'=> 'nullable|string|max:255',
            'chabot_Region4B'=> 'nullable|string|max:255',
            'chabot_Region5'=> 'nullable|string|max:255',
            'chabot_NCR'=> 'nullable|string|max:255',
            'chabot_CAR'=> 'nullable|string|max:255',
        ]);
        
        $chat_response = chatbot_response::create($validated);
        return response()->json($chat_response, 201);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(chatbot_response $chatbot_response)
    {
        return response()->json($chatbot_response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, Request $request, chatbot_response $chatbot_response)
    {

        $request->validate([
            'chabot_BWHours' => 'nullable|string|max:255',
            'chabot_BPDescription' => 'nullable|string|max:255',
            'chabot_Lazada'=> 'nullable|string|max:255',
            'chabot_Shopee'=> 'nullable|string|max:255',
            'chabot_Region1'=> 'nullable|string|max:255',
            'chabot_Region2'=> 'nullable|string|max:255',
            'chabot_Region3'=> 'nullable|string|max:255',
            'chabot_Region4A'=> 'nullable|string|max:255',
            'chabot_Region4B'=> 'nullable|string|max:255',
            'chabot_Region5'=> 'nullable|string|max:255',
            'chabot_NCR'=> 'nullable|string|max:255',
            'chabot_CAR'=> 'nullable|string|max:255',
        ]);
        $chatbots = chatbot_response::find($id);
        $chatbots->chabot_BWHours = $request->input('chabot_BWHours');
        $chatbots->chabot_BPDescription = $request->input('chabot_BPDescription');
        $chatbots->chabot_Lazada = $request->input('chabot_Lazada');
        $chatbots->chabot_Shopee = $request->input('chabot_Shopee');
        $chatbots->chabot_Region1 = $request->input('chabot_Region1');
        $chatbots->chabot_Region2 = $request->input('chabot_Region2');
        $chatbots->chabot_Region3 = $request->input('chabot_Region3');
        $chatbots->chabot_Region4A = $request->input('chabot_Region4A');
        $chatbots->chabot_Region4B = $request->input('chabot_Region4B');
        $chatbots->chabot_Region5 = $request->input('chabot_Region5');
        $chatbots->chabot_NCR= $request->input('chabot_NCR');
        $chatbots->chabot_CAR = $request->input('chabot_CAR');
        $chatbots->save();

        return response()->json(data: ['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    
}
