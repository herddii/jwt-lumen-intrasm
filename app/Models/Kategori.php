<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Kategori extends Model{
    
    protected $connection="intrasm";
    protected $table="kategori";

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'kategori.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'kategori.view', [ 'id' => $this->id ] );
    }

    public function setKategoriNameAttribute($value){
        $this->attributes['kategori_name']=ucfirst($value);
    }
}