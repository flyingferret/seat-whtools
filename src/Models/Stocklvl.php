<?php

namespace FlyingFerret\Seat\WHTools\Models;

use Illuminate\Database\Eloquent\Model;

class Stocklvl extends Model
{
    public $timestamps = true;
    
    protected $table = 'whtools_stocklvls';
    
    protected $fillable = ['id','minLvl','fitting_id'];
    
    
    public function fitting(){
        return $this->hasOne('Denngarr\Seat\Fitting\Models\Fitting','id','fitting_id');
    }
}
