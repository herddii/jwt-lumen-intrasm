<?php

namespace App\Models\Saleskit;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
	protected $connection="intrasm";
    //protected $connection="intranew";
    protected $table="tbl_program_genre";
    protected $primaryKey="id_genre";

    public function program(){
        return $this->hasMany('App\Models\Saleskit\Program','id_genre');
    }
}
