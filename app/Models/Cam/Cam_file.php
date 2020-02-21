<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_file extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_file";
    protected $primaryKey="id_cam_file";
    //const CREATED_AT ="INSERT_DATE";
    //const UPDATED_AT ="UPDATE_DATE";
    use SoftDeletes;
    //protected $dates = ['DELETED_AT'];
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;

    public function File_type_saleskit(){
        return $this->hasOne('App\Models\Cam\Cam_content', 'id_content', 'id_file');
    }

    public function File_type_sam(){
        return $this->hasOne('App\Models\Sam\Sam', 'id_sam', 'id_file');
    }    
}