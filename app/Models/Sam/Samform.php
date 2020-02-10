<?php
namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Samform extends Model{
	protected $table="sam_form";
	protected $primaryKey="id";

	public function parameter(){
		return $this->belongsTo('App\Models\Sam\Parameter','id_parameter');
	}
}
