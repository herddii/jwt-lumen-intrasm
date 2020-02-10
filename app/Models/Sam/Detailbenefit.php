<?php

namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Detailbenefit extends Model
{
    protected $table="sam_detail_benefit";
    protected $primaryKey="id";

    public function sam(){
        return $this->belongsTo('App\Models\Sam\Sam','id_sam');
    }

    public function status(){
        return $this->belongsTo('App\Models\Sam\Status','id_status');
    }

    public function benefit(){
        return $this->belongsTo('App\Models\Intrasm\Benefit','id_benefit');
    }
}
