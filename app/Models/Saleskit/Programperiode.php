<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;

class Programperiode extends Model
{
    protected $connection="intrasm";
    //protected $connection="intranew";
    protected $table="tbl_program_periode";
    protected $primaryKey="id_program_periode";

    function sam(){
        return $this->hasOne('App\Models\Sam\Sam','id_program_periode');
    }

    public function program(){
        return $this->belongsTo('App\Models\Saleskit\Program','id_program','id_program')
            ->select(
                [
                    'id_program',
                    'program_name',
                    'id_bu',
                    'id_channel',
                    'id_genre',
                    'mediakit',
                    'updated_at',
                    'deleted_at',
                    'id_type_event'
                ]
            )
            ->whereNull('deleted_at')
            ->where('mediakit',1);
    }

    public function samprogram(){
        return $this->belongsToMany('App\Models\Sam\Sam','sam_program','id_sam','id_program_periode');
    }

    public function mapping(){
        return $this->belongsToMany('App\Models\Mediakit\Mapping','tbl_program_periode','id_source');
    }

    public function season(){
        return $this->belongsTo('App\Models\Mediakit\Season','id_season')
            ->select(['id_season','season_name']);
    }

    public function category(){
        return $this->belongsTo('App\Models\Mediakit\Category','id_category')
            ->select(['id_category','category_name']);
    }

    public function pic(){
        return $this->hasMany('App\Models\Mediakit\Programpic','id_program_periode');
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

    public function files(){
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
                    'updated_at',
                    'id_bu'
                ]
                );
    }

    public function section(){
        return $this->belongsToMany('\App\Models\Section','tbl_program_pic','id_program_periode','id_section')
            ->select(
                [
                    'tbl_user_section.id_section',
                    'section_name'
                ]
            )->withPivot('id_program_pic','id_program_periode','id_section','id_user');
    }

    public function contentnew(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
            ->where('id_master_filetype',8)
            ->whereNull('deleted_at')
            ->where('mediakit',1);
    }

    public function hotspot(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
                'content_title','content_file_download','id_filetype','deleted_at','updated_at'
                )
        ->whereNull('deleted_at')
        ->where('mediakit',1)
        ->where('id_filetype',12)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');
        
    }

    public function sumhotspot(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->whereNull('deleted_at')
        ->where('mediakit',1)
        ->where('id_filetype',12)
        ->where('id_master_filetype',8)
        ->groupBy('id_program_periode');
        
    }
    
    public function sumhighlight(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->whereNull('deleted_at')
        ->where('mediakit',1)
        ->where('id_filetype',11)
        ->where('id_master_filetype',8)
        ->groupBy('id_program_periode');
    }

    public function highlight(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',11)
        ->where('mediakit',1)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');
    }

    public function presentation(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
        'content_title','content_file_download','deleted_at','updated_at')
        ->whereNull('deleted_at')
        ->whereIn('id_filetype',[183])
        ->where('mediakit',1)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');
    }

    public function sumpresentation(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->whereNull('deleted_at')
        ->whereIn('id_filetype',[183])
        ->where('mediakit',1)
        ->where('id_master_filetype',8)
        ->groupBy('id_program_periode');
    }
    public function program_m(){
        return $this->belongsTo('App\Models\Saleskit\Program','id_program','id_program')
            ->select(
                [
                    'tbl_program.id_program',
                    'program_name',
                    'tbl_program.id_genre',
                    'tbl_program.mediakit',
                    'tbl_program.id_bu',
                    'tbl_program.updated_at',
                    'id_type_event',
                    'b.BU_SHORT_NAME',
                    'c.genre_name',
                    'e.filetype_name'
                ]
            )->leftJoin('tbl_bu as b','b.id_bu','tbl_program.id_bu')
            ->leftJoin('tbl_program_genre as c','c.id_genre','tbl_program.id_genre')
            ->leftJoin('tbl_content as d','d.id_program_periode','id_program_periode')
            ->leftJoin('tbl_filetype as e','e.id_filetype','d.id_filetype');
    }
    public function hotspot_m(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'tbl_content.id_filetype',
                'content_title','content_file_download','tbl_content.deleted_at','id_bu','filetype_name','updated_at')
            ->leftJoin('tbl_filetype as e','e.id_filetype','tbl_content.id_filetype')
        ->where('tbl_content.id_filetype',12)
        ->whereNull('tbl_content.deleted_at')
        ->where('tbl_content.id_master_filetype',8);
    }
    public function presentation_m(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'tbl_content.id_filetype',
                'content_title','content_file_download','tbl_content.deleted_at','id_bu','filetype_name','updated_at')
            ->leftJoin('tbl_filetype as e','e.id_filetype','tbl_content.id_filetype')
        ->where('tbl_content.id_filetype',15)
        ->whereNull('tbl_content.deleted_at')
        ->where('tbl_content.id_master_filetype',8);
    }
    public function paket_m(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'tbl_content.id_filetype',
                'content_title','content_file_download','tbl_content.deleted_at','id_bu','filetype_name','updated_at')
            ->leftJoin('tbl_filetype as e','e.id_filetype','tbl_content.id_filetype')
        ->where('tbl_content.id_filetype',19)
        ->whereNull('tbl_content.deleted_at')
        ->where('tbl_content.id_master_filetype',8);
    }
    public function presentationpdf(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',15)
        ->where('mediakit',1)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');
    }
    public function sumpresentationpdf(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->where('id_filetype',15)
        ->where('mediakit',1)
        ->where('id_master_filetype',8)
        ->groupBy('id_program_periode');
    }

    public function paket(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype','mediakit',
        'content_title','content_file_download','deleted_at','updated_at')
        ->whereNull('deleted_at')
        ->where('mediakit',1)
        ->where('id_filetype',19)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');
    }

    public function sumpaket(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->whereNull('deleted_at')
        ->where('mediakit',1)
        ->where('id_filetype',19)
        ->where('id_master_filetype',8)
        ->groupBy('id_program_periode');
    }

    public function trailer(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',141)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');

    }

    public function sumtrailer(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->where('id_filetype',141)
        ->where('id_master_filetype',8)
        ->orderBy('id_program_periode');

    }

    public function potency(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype',
        'content_title','content_file_download','deleted_at','updated_at')
        ->whereNull('deleted_at')
        ->where('id_filetype',10)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');
    }

    public function sumpotency(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->whereNull('deleted_at')
        ->where('id_filetype',10)
        ->where('id_master_filetype',8)
        ->groupBy('id_program_periode');
    }

    public function flayer(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_content', 'id_program_periode', 'id_filetype',
        'content_title','content_file_download','updated_at')
        ->where('id_filetype',81)
        ->where('id_master_filetype',8)
        ->orderBy('updated_at','desc');

    }

    public function sumflayer(){
        return $this->hasMany('\App\Models\Saleskit\Contentnew','id_program_periode')
        ->select('id_program_periode','id_filetype')
        ->where('id_filetype',81)
        ->where('id_master_filetype',8)
        ->groupBy('id_program_periode');

    }

    public function artis(){
        return $this->belongsToMany('\App\Models\Saleskit\Artis','tbl_program_artis','id_program_periode','id_artis')
        ->select(
            [
                'tbl_program_artis.id_artis',
                'nama_artis'
            ]
        )->withPivot('id_program_artis','id_program_periode','id_artis');
    }
    
}
