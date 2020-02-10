<?php
namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Filedetailparameter extends Model{
	protected $table="sam_file_detail_parameter";
	protected $primaryKey="id";

    public $timestamps=false;
    public $incrementing=false;

    public function file(){
        return $this->belongsTo('App\Models\Sam\File','id_sam_file');
    }

    public function parameter(){
        return $this->belongsToMany('App\Models\Sam\Fileparameter','id_sam_file_parameter');
    }
}
