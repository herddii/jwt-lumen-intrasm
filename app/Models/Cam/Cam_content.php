<?php

namespace App\Models\Cam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_content extends Model
{
    protected $table="tbl_content";
    protected $primaryKey="id_content";

    use SoftDeletes;
    protected $fillable = [
        'insert_user',
        'update_user'
    ];

    public $timestamps = false;
}
