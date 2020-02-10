<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contentnew extends Model
{
    use SoftDeletes;

    //protected $connection="intranew";
    protected $connection="intrasm";
    protected $table="tbl_content";
    protected $primaryKey="id_content";

    public function masterfiletype(){
        return $this->belongsTo('App\Models\Saleskit\Masterfiletype','id_master_filetype')
            ->select(
                [
                    'id_master_filetype',
                    'title',
                    'master_group'
                ]
            );
    }

    public function filetype(){
        return $this->belongsTo('App\Models\Saleskit\Filetype','id_filetype')
            ->select(
                [
                    'id_filetype',
                    'id_master_filetype',
                    'filetype_name',
                    'folder',
                    'id_typespot',
                    'type',
                    //'f_client',
                    //'Mediakit',
                    //'video',
                    //'client',
                    'ext_file'
                ]
            );
    }


    public function filetype_typespot(){
        return $this->belongsTo('App\Models\Saleskit\Filetype','id_filetype')
            ->where('type','typespot');
    }

    public function section(){
        return $this->belongsTo('App\Models\Section','id_section')
            ->select(
                [
                    'id_section',
                    'section_name'
                ]
            );
    }

    public function periode(){
        return $this->belongsTo('App\Models\Saleskit\Programperiode','id_program_periode');
    }

    public function salestools(){
        return $this->belongsTo('App\Models\Saleskit\Salestool','id_salestools');
    }

     public function benefit(){
        return $this->belongsTo('App\Models\Intrasm\Benefit','id_benefit')
            ->select(['id_benefit','description','nama_benefit']);
    }

     public function bu(){
        return $this->belongsTo('App\Models\Bu','id_bu','id_bu')
            ->select(['id_bu','bu_short_name']);
    }

    // public function ratecard(){
    //     return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
    //     ->select('id_content', 'id_filetype',
    //             'content_title','content_file_download')
    //     ->where('id_master_filetype','=',1)
    //     ->where('id_filetype','=',85);
    // }

    public function ratecard(){
        return $this->belongsTo('App\Models\Saleskit\Masterfiletype','id_filetype');
    }

}
