<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\chatbot_response; // Import the model

class ChatbotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create records with null values for all fields except the primary key
        chatbot_response::create([
            'chabot_BWHours' => null,
            'chabot_BPDescription' => null,
            'chabot_Lazada' => null,
            'chabot_Shopee' => null,
            'chabot_Region1' => null,
            'chabot_Region2' => null,
            'chabot_Region3' => null,
            'chabot_Region4A' => null,
            'chabot_Region4B' => null,
            'chabot_Region5' => null,
            'chabot_NCR' => null,
            'chabot_CAR' => null,
        ]);

       
    }
}
