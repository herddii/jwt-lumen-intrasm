<?php

namespace App\Models\Intrasm;

use Illuminate\Database\Eloquent\Model;

class Section extends Model {
    
    protected $table="tbl_user_section";
    protected $primaryKey="id_section";

    public function picsection(){
    	return $this->hasOne('App\Models\Picsection','id_section');
    }

    public function user(){
    	return $this->hasOne('App\User','id_section');
    }

    public function user_sections(){
    	return $this->hasMany('App\Models\User','id_section');
    }

    public function users(){
        return $this->hasMany('App\Models\User','id_section')
            ->where('active',1)
            ->select(['user_id','username','user_name','first_name','id_section']);
    }

    public function parent(){
        return $this->belongsTo('App\Models\Section','id_parent')
            ->select(['id_section','section_name','id_parent','dept','section_category']);
    }

    public function bu(){
        return $this->belongsTo('App\Bu','ID_BU');
    }

    public function benefits(){
        return $this->belongsToMany('App\Models\Sam\Benefit','sam_benefit_section','id_section','id_benefit')
            ->withPivot(['id_section','id_benefit','id_req_type','id_bu']);
    }

    public function department(){
        return $this->belongsTo('App\Models\Userdepartment','ID_DEPARTEMENT');
    }

}
