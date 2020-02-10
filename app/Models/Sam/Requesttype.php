<?php

namespace App\Models\Sam;
use Illuminate\Database\Eloquent\Model;

class Requesttype extends Model
{

    protected $table="sam_request_type";
    protected $primaryKey="id_req_type";

    //public $timestamps=false;
    public $incrementing=false;

    public function sam(){
        return $this->hasOne('App\Models\Sam\Sam','req_type');
    }

    public function benefits(){
        return $this->belongsToMany('App\Models\Intrasm\Benefit','sam_benefit','id_req_type','id_benefit')
        ->orderBy('orders')
        ->withPivot(['id_benefit','id_req_type','required']);
    }

    public function forms(){
        return $this->belongsToMany('App\Models\Sam\Parameter','sam_form','id_parameter','id_req_type');
    }
}
