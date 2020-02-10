<?php

namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Subrequest extends Model
{
    protected $table="sam_sub_req_type";
    protected $primaryKey="id";

    public $timestamps=false;

    public function sam(){
        return $this->hasOne('App\Models\Sam\Sam','id_sub_req_type');
    }

    public function requesttype(){
        return $this->belongsTo('App\Models\Sam\Requesttype','id_req_type');
    }
}
