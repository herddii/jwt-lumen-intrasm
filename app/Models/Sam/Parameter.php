<?php
namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model{

	protected $table="sam_parameter";
	protected $primaryKey="id";

	public function detail_parameter(){
		return $this->hasOne('App\Models\Sam\Parameterdetail','id_sam_detail_parameter');
	}
}
