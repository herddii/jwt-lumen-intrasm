<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_cost extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_cost";
    protected $primaryKey="id_cam_cost";
    //const CREATED_AT ="INSERT_DATE";
    //const UPDATED_AT ="UPDATE_DATE";
    use SoftDeletes;
    //protected $dates = ['DELETED_AT'];
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;


    public function Nama_cost(){
        return $this->hasMany('App\User', 'USER_ID', 'cost_by')
        ->select(['USER_ID', 'USER_NAME']);
    }

    public function Nama_client(){
        return $this->hasOne('App\Models\Intrasm\Userclient', 'id_client', 'client')
        ->select(['id_client', 'firstname', 'lastname', \DB::raw('CONCAT(firstname, " ", lastname) AS full_name')]);
    }

    public function editNama_entertaiment(){
        return $this->hasOne('App\User', 'USER_ID', 'cost_by')
        ->select('USER_ID', 'USER_NAME');
    }    
}