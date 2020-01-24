<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Dompetkategori extends Model{
    
    protected $connection="intrasm";
    protected $table="dompet_kategori";

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'dompet_kategori.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'dompet_kategori.view', [ 'id' => $this->id ] );
    }

    public function setDompetKategoriNameAttribute($value){
        $this->attributes['dompet_kategori_name']=ucfirst($value);
    }
}