<?php

namespace App\Models\Intrasm;

use Illuminate\Database\Eloquent\Model;

class Userdepartment extends Model
{
    protected $table="tbl_user_department";
    protected $primaryKey="ID_DEPARTEMENT";

    public $timestamps=false;

    public function user(){
        return $this->hasOne('App\User','ID_DEPARTEMENT');
    }
}
