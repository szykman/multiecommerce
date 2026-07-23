<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{


protected $fillable = [

'store_id',
'product_id',
'media_id',
'position',
'is_primary'

];


protected $casts = [

'is_primary'=>'boolean'

];



public function media()
{

return $this->belongsTo(Media::class);

}


public function product()
{

return $this->belongsTo(Product::class);

}


}
