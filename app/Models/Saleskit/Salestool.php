<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;

class Salestool extends Model
{
    protected $connection="intrasm";
    //protected $connection="intranew";
    protected $table="tbl_salestools";
    protected $primaryKey="id_salestools";
    public $timestamps=false;

    public function ta(){
        return $this->belongsToMany('App\Models\Saleskit\Ta','tbl_salestools_ta','id_salestools','id_ta')
            ->select(['tbl_target_audience.id_ta','ta_name']);
    }

    public function filetype(){
        return $this->belongsTo('App\Models\Saleskit\Masterfiletype','id_master_filetype')
            ->select(['
                id_master_filetype',
                'title',
                'master_group',
                'id_filetype',
                'folder',
                'ext_file']);
    }

    public function cover(){
        return $this->belongsTo('\App\Models\Saleskit\Contentnew','content_use','id_content')
        ->select(['id_content',
                'content_title',
                'id_program_periode',
                'id_master_filetype',
                'year_of_file',
                'month_of_file',
                'content_desc',
                'content_file_download',
                'content_headline',
                'content_start_date',
                'content_end_date',
                'id_bu',
                'updated_at',
                'id_filetype'
            ]);
    }

    public function filesalestools(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype',
                'content_title','content_file_download','updated_at')
        ->where('id_master_filetype',22);
    }
     public function content_m(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select(
                [
                    'id_content',
                    'id_program_periode',
                    'id_master_filetype',
                    'id_filetype',
                    'id_section',
                    'content_title',
                    'year_of_file',
                    'month_of_file',
                    'content_start_date',
                    'content_end_date',
                    'content_desc',
                    'content_file_download',
                    'mediakit',
                    'id_bu',
                    'updated_at'
                ]
            )
        ->where('mediakit',1)
        ->whereNull('deleted_at');
    }

    public function content(){
        return $this->belongsTo('\App\Models\Saleskit\Contentnew','content_use','id_content')
            ->select(
                [
                    'id_content',
                    'id_program_periode',
                    'id_master_filetype',
                    'id_filetype',
                    'id_section',
                    'content_title',
                    'year_of_file',
                    'month_of_file',
                    'content_start_date',
                    'content_end_date',
                    'content_desc',
                    'content_file_download',
                    'mediakit',
                    'updated_at',
                    'id_bu'
                ]
            );
    }

    public function performance(){
        return $this->belongsTo('\App\Models\Saleskit\Contentnew','id_salestools','id_program_periode')
            ->select(
                [
                    'id_content',
                    'id_program_periode',
                    'id_master_filetype',
                    'id_filetype',
                    'id_section',
                    'content_title',
                    'year_of_file',
                    'month_of_file',
                    'content_start_date',
                    'content_end_date',
                    'content_desc',
                    'content_file_download',
                    'mediakit',
                    'portal',
                    'updated_at',
                    'id_bu'
                ]
            );

    }

    public function ratepdf(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','id_bu',
        'content_title','content_file_download','mediakit','updated_at')
        ->where('mediakit',1)
        ->where('id_filetype',16)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',1);
    }
    public function sumratepdf(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','id_bu',
        'content_title','content_file_download','mediakit','updated_at')
        ->where('mediakit',1)
        ->where('id_filetype',16)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',1)
        ->groupBy('id_program_periode');
    }

    public function ratehotspot(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','id_bu',
        'content_title','content_file_download','mediakit','updated_at')
        ->where('mediakit',1)
        ->where('id_filetype',85)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',1);
    }
    public function sumratehotspot(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','id_bu',
        'content_title','content_file_download','mediakit','updated_at')
        ->where('mediakit',1)
        ->where('id_filetype',85)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',1)
        ->groupBy('id_program_periode');
    }

    public function spcpdf(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',152)
        ->where('mediakit',1)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',2);
    }

    public function spchotspot(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',14)
        ->where('mediakit',1)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',2);
    }

    public function sumspchotspot(){
        return $this->hasOne('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',14)
        ->where('mediakit',1)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',2);
    }

    public function fileperformance(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',21)
        ->where('mediakit',1)
        ->whereNull('deleted_at')
        ->where('id_master_filetype',21);
    }

     public function fileratecard(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','id_bu',
        'content_title','content_file_download','mediakit','updated_at')
        ->where('mediakit',1)
        ->whereIn('id_filetype',[16,85])
        ->whereNull('deleted_at')
        ->where('id_master_filetype',1);
    }



}
