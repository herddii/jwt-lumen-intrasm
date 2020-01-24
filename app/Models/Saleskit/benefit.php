<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    //protected $connection="intranew";
    protected $connection="intrasm";
    protected $table="benefits";
    protected $primaryKey="id_benefit";

    public function typespot(){
        return $this->hasMany('App\Models\Saleskit\Benefit_typespot','id_benefit')
        ->where('keterangan',1);
    }    
}
