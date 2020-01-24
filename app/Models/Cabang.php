<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cabang extends Model{
    
    protected $connection="intrasm";
    protected $table="cabang";

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'cabang.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'cabang.view', [ 'id' => $this->id ] );
    }

    public function setCabangNameAttribute($value){
        $this->attributes['nama']=ucfirst($value);
    }

    public function bisnis(){
        return $this->belongsTo('App\Models\Bisnis');
    }

    public function user(){
        return $this->belongsToMany('\App\User','user_cabang');
    }
}