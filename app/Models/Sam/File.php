
<?php

namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table="sam_file";
    protected $primaryKey="id_sam_file";

    public $incrementing=false;

    public function sam(){
        return $this->belongsTo('App\Models\Sam\Sam','id_sam');
    }

    public function activity(){
        return $this->belongsTo('App\Models\Sam\Activity','id_activity');
    }

    public function status(){
        return $this->belongsTo('App\Models\Sam\Status','id_status');
    }

    public function approval(){
        return $this->belongsTo('App\User','approval_name','email');
    }

    public function user(){
        return $this->belongsTo('App\User','insert_user','email')
            ->select(array('email','username'));
    }

    public function detail_parameter(){
        return $this->hasOne('App\Models\Sam\Filedetailparameter','id_sam_file');
    }

    public function parameters(){
        return $this->belongsToMany('App\Models\Sam\Fileparameter','sam_file_detail_parameter','id_sam_file','id_sam_file_parameter')->withPivot(['id_sam_file','id_sam_file_parameter','value']);
    }
}
