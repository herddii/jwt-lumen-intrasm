<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_client extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_client";
    protected $primaryKey="id";
    //const CREATED_AT ="INSERT_DATE";
    //const UPDATED_AT ="UPDATE_DATE";
    use SoftDeletes;
    //protected $dates = ['DELETED_AT'];
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;

    public function Edit_idclientaccount(){
        return $this->hasOne('App\Models\Intrasm\Userclient_account', 'id_client_account', 'id_client_account');
    }

    public function Cam_activity_history(){
        return $this->hasMany('App\Models\Cam\Cam_activity', 'id_cam', 'id_cam');
    }

    public function Cam_history(){
        return $this->belongsTo('App\Models\Cam\Cam', 'id_cam', 'id_cam');
    }

    public function Name_userclient_account(){
        return $this->belongsTo('App\Models\Intrasm\Userclient_account','id_client_account')
        ->select(['id_client', 'id_client_account'])
        ->where('active', 1)
        ->whereNull('deleted_at');
    }    
}