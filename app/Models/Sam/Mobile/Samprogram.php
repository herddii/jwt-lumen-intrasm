<?php

namespace App\Models\Sam\Mobile;

use Illuminate\Database\Eloquent\Model;

class Samprogram extends Model
{
    protected $table="mobile_sam_program";
    protected $primaryKey="id";


    public function periode(){
        return $this->belongsTo('App\Models\Intrasm\Programperiode','id_program_periode');
    }

    public function samprogram(){
        return $this->belongsTo('App\Models\Sam\Mobile\Sam','id_sam');
    }

}
