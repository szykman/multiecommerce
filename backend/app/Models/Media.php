<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [

        'store_id',

        'name',

        'title',

        'file',

        'type',

        'mime',

        'extension',

        'size',

        'width',

        'height',

        'folder',

        'alt',

        'caption',

        'metadata',

    ];

    protected $casts = [

        'metadata' => 'array'

    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/'.$this->file);
    }

}
