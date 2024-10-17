<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $primaryKey = 'website_ID';

    // If the primary key is not an incrementing integer
    public $incrementing = true; // Set to false if your primary key is not incrementing
    protected $keyType = 'int';
    protected $fillable = [
        'business_id',
        'product_id',
        'website_description',
        'website_details',
        'website_image',
        'about_us1',
        'about_us2',
        'about_us3',
        'website_footNote',
        'onSale_section',
        'featured_section'
        
    ];

}
