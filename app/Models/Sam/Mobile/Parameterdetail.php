<?php
namespace App\Models\Sam\Mobile;

use Illuminate\Database\Eloquent\Model;

class Parameterdetail extends Model{
	protected $table="mobile_sam_detail_parameter";
	protected $primaryKey="id";

    public function detail(){
        return $this->belongsTo('App\Models\Sam\Parameter','id_parameter');
    }

    public function sam(){
        return $this->belongsToMany('App\Models\Sam\Mobile\Sam','id_sam');
    }
}
