<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Set the primary key column name
    public $incrementing = true; // Set to true if the primary key is auto-incrementing
    protected $keyType = 'int'; // Set the key type (string, integer, etc.) based on your primary key column type

    protected $fillable = [
        //FOR FINANCE
        'description',
        'date',
        'category',
        'amount',
        
    
    ];
}

