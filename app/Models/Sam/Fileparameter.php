<?php
namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Fileparameter extends Model{
	protected $table="sam_file_parameter";
	protected $primaryKey="id";

    public function detail(){
        return $this->hasMany('\App\Models\Sam\Filedetailparameter','id_sam_file_parameter');
    }
}
