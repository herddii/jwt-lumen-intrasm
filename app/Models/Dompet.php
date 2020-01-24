<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Dompet extends Model{
    
    protected $connection="intrasm";
    protected $table="dompet";

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'dompet.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'dompet.view', [ 'id' => $this->id ] );
    }

    public function setDompetNameAttribute($value){
        $this->attributes['dompet_name']=ucfirst($value);
    }
}