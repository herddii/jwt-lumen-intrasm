<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_activity extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_activity";
    protected $primaryKey="id_activity";
    //const CREATED_AT ="INSERT_DATE";
    //const UPDATED_AT ="UPDATE_DATE";
    use SoftDeletes;
    //protected $dates = ['DELETED_AT'];
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;


    public function Cam_cost(){
        return $this->hasMany('App\Models\Cam\Cam_cost', 'id_activity', 'id_activity')
        ->select(['id_cam_cost', 'id_activity', 'cost', 'title', 'nota', 'cost_description', 'cost_by', 'client']);
    }

    public function Cam_typeactivity(){
        return $this->hasMany('App\Models\Cam\Cam_typeactivity', 'id_cam_typeactivity', 'id_cam_typeactivity')
        ->select(['id_cam_typeactivity', 'name_activity']);
    }

    public function Nama_task(){
        return $this->hasOne('App\User', 'USER_ID', 'insert_user')
        ->select(['USER_ID', 'USER_NAME']);
    }

    public function Cam_edit(){
        return $this->belongsTo('App\Models\Cam\Cam', 'id_cam', 'id_cam');
    }

    public function Cam_partner_edit(){
        return $this->hasMany('App\Models\Cam\Cam_partner', 'id_cam', 'id_cam');
    }

    public function Cam_client_edit(){
        return $this->hasOne('App\Models\Cam\Cam_client', 'id_cam', 'id_cam');
    }

    public function Cam_brand_edit(){
        return $this->hasOne('App\Models\Cam\Cam_brand', 'id_cam', 'id_cam');
    }

    public function Cam_typeactivity_edit(){
        return $this->hasOne('App\Models\Cam\Cam_typeactivity','id_cam_typeactivity','id_cam_typeactivity');
    }

    public function Cam_plan_report(){
        return $this->hasOne('App\Models\Cam\Cam','id_cam','id_cam');
    }

    public function Cam_file(){
        return $this->hasMany('App\Models\Cam\Cam_file', 'id_activity', 'id_activity');
    }    
}