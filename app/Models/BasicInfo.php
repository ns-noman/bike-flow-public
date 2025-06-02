<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicInfo extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'title',
        'meta_keywords',
        'meta_description',
        'logo',
        'favicon',
        'phone',
        'telephone',
        'fax',
        'email',
        'location',
        'address',
        'web_link',
        'facebook_link',
        'twitter_link',
        'linkedin_link',
        'youtube_link',
        'assets_value',
        'total_employees',
        'total_companies',
        'start_year',
        'map_embed',
        'video_embed_1',
        'video_embed_2',
        'video_embed_3',
    ];
}
