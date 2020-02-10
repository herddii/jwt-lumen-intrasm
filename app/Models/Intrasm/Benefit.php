<?php

namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model{

    protected $table="benefits";
    protected $primaryKey="id_benefit";

    public $timestamps=false;

    public function detailbenefit(){
        return $this->hasMany('App\Models\Sam\Detailbenefit','id_benefit')->select(['id_benefit','nama_benefit']);
    }    

    public function sections(){
        return $this->belongsToMany('App\Models\Intrasm\Section','sam_benefit_section','id_benefit','id_section')
            ->withPivot(['id_section','id_benefit','id_req_type','id_bu']);
    }
    public function typespot(){
        return $this->hasMany('App\Models\Saleskit\Benefit_typespot','id_benefit')
        ->where('keterangan',1);
    }
}
