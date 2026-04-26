<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageItem extends Model
{
    protected $fillable = [
        'section',
        'type',
        'key',
        'title',
        'subtitle',
        'description',
        'value',
        'link_label',
        'link_url',
        'image_path',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];
}
