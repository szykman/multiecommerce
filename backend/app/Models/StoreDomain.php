<?php

//namespace App\Models;

//use Illuminate\Database\Eloquent\Model;

//class StoreDomain extends Model
//{
    //
//}
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreDomain extends Model
{
    protected $fillable = [
        'store_id',
        'domain',
        'primary',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
