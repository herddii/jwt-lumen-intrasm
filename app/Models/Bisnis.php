<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bisnis extends Model{
    
    protected $connection="intrasm";
    protected $table="bisnis";

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'bisnis.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'bisnis.view', [ 'id' => $this->id ] );
    }

    public function setBisnisNameAttribute($value){
        $this->attributes['bisnis_name']=ucfirst($value);
    }

    public function category(){
        return $this->belongsToMany('\App\Models\Category','bisnis_category');
    }

     // this is a recommended way to declare event handlers
     public static function boot() {
        parent::boot();

        static::deleting(function($bisnis) {
             $bisnis->category()->detach();
        });
    }
}