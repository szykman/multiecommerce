<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [

        'store_id',

        'email',
        'phone',
        'whatsapp',
        'hours',

        'logo',
        'banner',
        'favicon',

        'primary_color',
        'secondary_color',
        'radius',

        'instagram',
        'facebook',
        'youtube',
        'tiktok',

        'seo_title',
        'seo_description',
        'seo_keywords',

        'google_maps',
        'copyright',
        'footer_text',

'theme',
'font',
'shadow',
'animations',

 // mantém para futuras configurações
'document',    
'settings',
    ];

protected $casts = [
    'settings' => 'array',
];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
