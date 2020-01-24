<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $connection="intrasm";
    //protected $connection="intranew";
    protected $table="tbl_program";
    protected $primaryKey="id_program";

    public function periode(){
        return $this->hasOne('App\Models\Saleskit\Programperiode','id_program')
            ->select(['id_program_periode','id_program','content_start_date','content_end_date',
                'onair_start_date','onair_end_date','date_onair',
                'id_season','id_category','content_use','content_trailer',
                'description','target','Mediakit','mediakit','active']);
    }

    public function genre(){
        return $this->belongsTo('App\Models\Saleskit\Genre','id_genre','id_genre');
    }

    public function bu(){
        return $this->belongsTo('App\Models\Bu','id_bu','id_bu')
            ->select(['id_bu','bu_short_name']);
    }

    public function ta(){
        return $this->belongsToMany('\App\Models\Saleskit\Ta','tbl_program_ta','id_program','id_ta','id_program','id_ta');
    }

    public function type_event (){
        return $this->belongsTo('App\Models\Saleskit\Programtypeevent','id_type_event','id_type_event');
    }

    public function periodes(){
        return $this->hasMany('App\Models\Saleskit\Programperiode','id_program')
            ->select(
                [
                    'id_program',
                    'id_program_periode',
                    'content_start_date',
                    'content_end_date'
                ]
            );
    }

    public function mapping(){
        return $this->hasMany('App\Models\Saleskit\Mapping','id_source');
    }

    public static function getMimeTypeImages()
    {
        return [
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];
    }


    public function ariana(){
        return $this->belongsTo('App\Models\Saleskit\Programmapping','id_program','id_source')
        ->where('type_mapping','=','ARIANA');
    }


}


