<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dailydata extends Model
{
    protected $fillable =[
    	'imp','click','cv','cvr','ctr','active','partnership','asp_id','product_id'
    ];

}
