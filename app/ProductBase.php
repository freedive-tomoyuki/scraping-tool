<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductBase extends Model
{
    public function product()
    {
        return $this->hasMany('App\Product');
    }
}
