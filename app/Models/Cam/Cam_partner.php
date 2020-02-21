<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_partner extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_partner";
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


    public function Name_partner(){
        return $this->belongsTo('App\User','user_id', 'USER_ID')
        ->select(['USER_ID', 'USER_NAME', 'IMAGES']);
    }    
}