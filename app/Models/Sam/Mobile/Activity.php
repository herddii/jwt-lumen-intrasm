<?php

namespace App\Models\Sam\Mobile;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table="mobile_sam_activity";
    protected $primaryKey="id_activity";

    //public $timestamps=false;

    public function sam(){
        return $this->belongsTo('App\Models\Sam\Mobile\Sam','id_sam');
    }

    public function user(){
        return $this->belongsTo('App\User','insert_user','USER_ID')
            ->select(array('username','USER_ID','user_name','images','position','id_section'));
    }

    public function status(){
        return $this->belongsTo('App\Models\Sam\Status','id_status')
            ->select(array('id_status','nama_status','id_req_type','dept_status','warning','stat'));
    }

    public function file(){
        return $this->hasMany('App\Models\Sam\Mobile\File','id_activity');
    }
    
}
