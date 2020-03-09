<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //protected $fillable =['product'];
	protected $table = 'products';

    public function asp()
    {
        return $this->belongsTo('App\Asp');
    }
    public function dailydata()
    {
        return $this->hasMany('App\Dailydata');
    }
}
