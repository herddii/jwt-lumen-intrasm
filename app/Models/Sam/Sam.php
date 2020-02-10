<?php

namespace App\Models\Sam;

use Illuminate\Database\Eloquent\Model;

class Sam extends Model
{
    protected $table="sam";
    protected $primaryKey="id_sam";

    public $incrementing=false;

    public function picsam(){
        return $this->belongsTo('App\User','pic_sam','user_id')
            ->SELECT(ARRAY('user_id','username','user_name','images','position','id_section','id_bu'));
    }

    public function pic_creative(){
        return $this->hasMany('App\User','id_section','pic_section')
            ->select(array('user_id','username','user_name','images','position','id_section','id_bu'))
            ->where('active',1);
    }

    public function section(){
        return $this->belongsTo('App\Models\Intrasm\Section','pic_section','ID_SECTION')
            ->select('ID_SECTION','SECTION_NAME');
    }

    public function usermc(){
        return $this->belongsTo('App\User','ID_SECTION','user_id')
            ->select('id_section','user_id','username');
    }

    public function am(){
        return $this->belongsTo('App\User','pic_am','user_id')
            ->select(array('user_id','username','user_name','images','position','id_section','id_bu'));
    }

    public function tblam(){
        return $this->belongsTo('App\Models\Intrasm\Am','pic_am','id_am')
            ->select('id_am','id_sgm','id_sm','id_gm','id_bu');
    }

    public function periode(){
        return $this->belongsTo('App\Models\Saleskit\Programperiode','id_program_periode')
            ->select(array('id_program_periode','id_program'));
    }

    public function status(){
        return $this->belongsTo('App\Models\Sam\Status','id_status')
            ->select(array('id_status','nama_status','id_req_type','relasi_status',
            'dept_status','warning','stat'));
    }

    public function statusprogress(){
        return $this->belongsTo('App\Models\Sam\Status','id_status_progress','id_status')
            ->select(array('id_status','nama_status','id_req_type','relasi_status',
            'dept_status','warning','stat'));
    }

    public function progressUpdateUser(){
        return $this->belongsTo('App\User','update_progress_user','user_id')
            ->select(array('user_id','username','user_name','images','position','id_section','id_bu'));
    }

    public function brand(){
        return $this->belongsTo('App\Models\Intrasm\Brand','id_brand')
            ->select(array('id_brand','nama_brand'));
    }

    public function advertiser(){
        return $this->belongsTo('App\Models\Intrasm\Advertiser','id_advg')
            ->select(array('id_adv','nama_adv'));
    }

    public function agency(){
        return $this->belongsTo('App\Models\Intrasm\Agencypintu','id_apu')
            ->select(array('id_agcyptu','nama_agencypintu','id_agcy'))
            ->orderBy('nama_agencypintu');
    }

    public function agencypintu(){
        return $this->belongsTo('App\Models\Intrasm\Agencypintu','id_apu');
    }

    public function request(){
        return $this->belongsTo('App\Models\Sam\Requesttype','id_req_type')
            ->select(array('id_req_type','singkatan','nama'));
    }

    public function subrequest(){
        return $this->belongsTo('App\Models\Sam\Subrequest','id_sub_req_type');
    }

    public function activity(){
        return $this->hasMany('App\Models\Sam\Activity','id_sam')
            ->orderBy('id_activity','desc');
    }


    public function file(){
        return $this->hasOne('App\Models\Sam\File','id_sam');
    }

    public function benefit(){
        return $this->belongsToMany('App\Models\Intrasm\Benefit','sam_detail_benefit','id_sam','id_benefit')
            ->withPivot(['id','id_sam','id_benefit','id_typespot','id_status','onair_date','deadline_tayang',
                'deadline_mkt','versi_brand','materi','barcode','budget','budget_mkt','cost','pic_sam','insert_user']);
    }

    public function approved(){
        return $this->belongsTo('App\Models\Intrasm\Am','PIC_AM','id_am');
    }


    public function approve(){
            return $this->belongsTo('App\User','approved_by','user_id')
        ->select(['user_id','USER_NAME']);

    }

    public function approveSgm(){
            return $this->belongsTo('App\User','approved_sgm','user_id')
        ->select(['user_id','USER_NAME']);

    }

    public function approveSm(){
            return $this->belongsTo('App\User','approved_sm','user_id')
        ->select(['user_id','USER_NAME']);
    }

    public function approveMc(){
            return $this->belongsTo('App\User','approved_mc','user_id')
        ->select(['user_id','USER_NAME']);
    }

    public function samprogram(){
        return $this->belongsToMany('App\Models\Saleskit\Programperiode','sam_program','id_sam','id_program_periode')
            ->select(['sam_program.id_program_periode','id_program']);
    }

    public function detailbenefit(){
        return $this->belongsToMany('App\Models\Intrasm\Benefit','sam_detail_benefit','id_sam','id_benefit')
            ->select(array('sam_detail_benefit.id_benefit','nama_benefit','id_status'))
            ->withpivot('id_sam','id_benefit','budget','budget_mkt','cost');
    }

    public function insertByUser(){
        return $this->belongsTo('App\User','insert_user','user_id')
            ->select(array('user_id','username','user_name','images','position','id_section','id_bu'));
    }

    public function parameters(){
        return $this->belongsToMany('App\Models\Sam\Parameter','sam_detail_parameter','id_sam','id_parameter')
            ->withPivot('id_sam','id_parameter','value');
    }

    public function requesttype(){
        return $this->belongsTo('App\Models\Sam\Requesttype','id_req_type');
    }

    public function bu(){
        return $this->belongsTo('App\Models\Bu','id_bu')
            ->select(['ID_BU','bu_short_name']);
    }
}
