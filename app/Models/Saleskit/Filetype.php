<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;

class Filetype extends Model
{
    protected $connection="intrasm";
    protected $table="tbl_filetype";
    protected $primaryKey="id_filetype";

    public function masterfiletype(){
        return $this->belongsTo('App\Models\Saleskit\Masterfiletype','id_master_filetype')
            ->select(['id_master_filetype','title','master_group']);
    }

    public function typespot(){
    	return $this->belongsTo('App\Models\Intrasm\Benefit_typespot','id_typespot');
    }

    public function content(){
    	return $this->hasMany('App\Models\Saleskit\Contentnew','id_filetype')
        ->where('mediakit',1)
        ->whereNull('deleted_at');
    }

    public function contentTypespot(){
        return $this->hasOne('App\Models\Saleskit\Contentnew','id_filetype')
        ->where('mediakit',1)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',3)
        ->orderBy('updated_at','desc');
    }


    public function contentbyunit(){
        $date = date_create(date('Y-m-d H:i:s')); 
        date_sub($date, date_interval_create_from_date_string('3 month')); 
        $interval = date_format($date, 'Y-m-d H:i:s'); 
        
        return $this->hasMany('App\Models\Saleskit\Contentnew','id_filetype')
        ->where('mediakit',1)
        ->where('id_bu',\Auth::user()->ID_BU)
        ->where('created_at', '>=',$interval)
        ->whereNull('deleted_at');
    }

}
