<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_typeactivity extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_type_activity";
    protected $primaryKey="id_cam_typeactivity";
    //const CREATED_AT ="INSERT_DATE";
    //const UPDATED_AT ="UPDATE_DATE";
    use SoftDeletes;
    //protected $dates = ['DELETED_AT'];
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;    
}