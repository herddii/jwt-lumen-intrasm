<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
class Portalberita extends Model
{
    
    protected $table="portal_berita";
    protected $primaryKey="id_portal";
    use SoftDeletes;

    public function portal_tag(){
        return $this->belongsToMany('App\Models\Saleskit\Portaltagmaster','portal_tag','id_portal','id_tag')
            ->select(array('portal_tag_master.id_tag','nama_tag'))
            ->withpivot('id_portal','id_tag');
    }

    public function sector(){
        return $this->belongsTo('App\Models\Intrasm\Sector','id_sector')->select('id_sector','name_sector');
    }


    public function bankfoto(){
        return $this->belongsTo('App\Models\Saleskit\Portal_bankfoto','id_portal','id_portal')
        ->groupBy('id_portal');
    }

    // public function portal_kategori(){
    // 	return $this->belongsTo('App\Models\Saleskit\Portalkategori','id_kategori');
    // }


    public function kategori(){
        return $this->belongsTo('App\Models\Saleskit\Portalkategori','id_kategori');
    }

    public function advertiser(){
        return $this->belongsToMany('App\Models\Intrasm\Advertiser','portal_advertiser','id_portal','id_adv')
            ->select(array('db_m_advertiser.id_adv','nama_adv'))
            ->withpivot('id_portal','id_adv');
    }

    public function brand(){
        return $this->belongsToMany('App\Models\Intrasm\Brand','portal_brand','id_portal','id_brand')
            ->select(array('db_m_brand.id_brand','nama_brand'))
            ->withpivot('id_portal','id_brand');
    }

    public function hotspot(){
        return $this->belongsTo('App\Models\Saleskit\Portalhotspot','id_portal','id_portal');
    }



}
