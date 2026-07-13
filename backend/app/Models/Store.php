<?php

//namespace App\Models;

//use Illuminate\Database\Eloquent\Model;

//class Store extends Model
//{
    //
//}
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'hero_banner',
        'active',
    ];

    public function domains()
    {
        return $this->hasMany(StoreDomain::class);
    }

public function media()
{
    return $this->hasMany(Media::class);
}

}
