<?php

namespace App\Models\Sam\Mobile;

use Illuminate\Database\Eloquent\Model;

class Detailbenefit extends Model
{
    protected $table="mobile_sam_detail_benefit";
    protected $primaryKey="id";

    public function sam(){
        return $this->belongsTo('App\Models\Sam\Mobile\Sam','id_sam');
    }

    public function status(){
        return $this->belongsTo('App\Models\Sam\Status','id_status');
    }

    public function benefit(){
        return $this->belongsTo('App\Models\Sam\Benefit','id_benefit');
    }
}
