<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [

        'store_id',

        'name',

        'file',

        'type',

        'mime',

        'extension',

        'size',

        'width',

        'height',

        'folder',

        'alt',

        'caption'

    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/'.$this->file);
    }

protected $casts = [

    'metadata'=>'array'

];

}
