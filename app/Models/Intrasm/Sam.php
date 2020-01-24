<?php

namespace App\Models\Intrasm;

use Illuminate\Database\Eloquent\Model;

class Sam extends Model
{
    protected $connection="intrasm";
    protected $table="sam";
    protected $primaryKey="id_sam";

    public $incrementing=false;
}
