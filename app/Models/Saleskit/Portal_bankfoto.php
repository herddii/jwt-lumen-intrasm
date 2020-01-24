<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;

class Portal_bankfoto extends Model
{
    protected $table="portal_bankfoto";
    protected $primaryKey="id";

    public function portalberita(){
        return $this->belongsTo('App\Models\Saleskit\Portalberita','id_portal');
    }
}
