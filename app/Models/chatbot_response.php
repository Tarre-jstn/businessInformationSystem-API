<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chatbot_response extends Model
{
    use HasFactory;

    protected $table = 'chatbot_response';

    // Tell Eloquent that the primary key is 'business_id'
    protected $primaryKey = 'chatbot_id';

    // Disable auto-increment if you are using a custom incrementing value
    public $incrementing = true;

    // Set the key type to 'bigint' since the business_id column is a big integer
    protected $keyType = 'int';

    // Allow mass assignment for these fields
    protected $fillable = [
        'chabot_BWHours',
        'chabot_BPDescription',
        'chabot_Lazada',
        'chabot_Shopee',
        'chabot_Region1',
        'chabot_Region2',
        'chabot_Region3',
        'chabot_Region4A',
        'chabot_Region4B',
        'chabot_Region5',
        'chabot_NCR',
        'chabot_CAR',
    ];
}

