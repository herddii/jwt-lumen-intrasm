<?php

namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table="sam_activity";
    protected $primaryKey="id_activity";

    //public $timestamps=false;

    public function sam(){
        return $this->belongsTo('App\Models\Sam\Sam','id_sam');
    }

    public function user(){
        return $this->belongsTo('App\User','insert_user','user_id')
            ->select(array('username','user_id','user_name','images','position','id_section','ID_DEPARTEMENT'));
    }

    public function status(){
        return $this->belongsTo('App\Models\Sam\Status','id_status')
            ->select(array('id_status','nama_status','id_req_type','dept_status','warning','stat','private'));
    }

    public function file(){
        return $this->hasMany('App\Models\Sam\File','id_activity');
    }
    
}
