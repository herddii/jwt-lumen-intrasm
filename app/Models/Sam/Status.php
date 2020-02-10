<?php

namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table="sam_status";
    protected $primaryKey="id_status";

    public $timestamps=false;

    public function sam(){
        return $this->hasOne('App\Models\Sam\Sam','id_status');
    }

    public function activity(){
        return $this->hasOne('App\Models\Sam\Activity','id_status');
    }

    public function req_type(){
        return $this->belongsTo('App\Models\Sam\Requesttype','id_req_type')
            ->select(['id_req_type','singkatan','nama']);
    }

    public function filetype(){
        return $this->belongsTo('App\Models\Sam\Filetype','file_type_id');
    }

    public function detailbenefit(){
        return $this->hasOne('App\Models\Sam\Detailbenefit','id_sam');
    }

    public function file(){
        return $this->hasOne('App\Models\Sam\File','id_status');
    }
}
