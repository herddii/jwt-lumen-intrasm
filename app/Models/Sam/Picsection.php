<?php

namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Picsection extends Model
{
    protected $table="sam_benefit_section";
    protected $primaryKey="id";

    public $timestamps=false;

    public function req_type(){
        return $this->belongsTo('App\Models\Sam\Requesttype','id_req_type');
    }

    public function users(){
        return $this->hasOne('App\User','ID_SECTION');
    }

    public function section(){
        return $this->belongsTo('App\Models\Intrasm\Section','ID_SECTION')
            ->select(
                [
                    'ID_SECTION','SECTION_NAME','ID_PARENT','DEPT','SECTION_CATEGORY',
                    'SECTION_GROUP','ID_BU'
                ]   
            );
    }
}
