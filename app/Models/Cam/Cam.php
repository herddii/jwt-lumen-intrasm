<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam extends Model
{
    // protected $connection="cam"; 
    protected $table="cam";
    protected $primaryKey="id_cam";
    //const CREATED_AT ="INSERT_DATE";
    //const UPDATED_AT ="UPDATE_DATE";
    use SoftDeletes;
    //protected $dates = ['DELETED_AT'];
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;

    public function Cam_activity(){
        return $this->hasMany('App\Models\Cam\Cam_activity', 'id_cam')
        ->select(['id_activity', 'id_ref_activity', 'id_cam', 'status', 'potency_revenue', 'id_cam_typeactivity', 'subject', 'description', 'location', 'start_date', 'end_date', 'insert_user', 'update_user', \DB::raw('date_format(start_date, "%M")as bulan_client_history, concat(if(round(week(start_date)/12)=0,1,round(week(start_date)/12)),",",monthname(start_date)," ",year(start_date)) as week, DATE(start_date) as days'), \DB::raw('date_format(start_date, "%d")as hari_client_history')])
        ->whereNull('deleted_at');
    }

    public function Cam_brand(){
        return $this->hasMany('App\Models\Cam\Cam_brand','id_cam', 'id_cam')
            ->select(['id', 'id_cam', 'id_brand']);
    }

    public function Agencypintu(){
       return $this->belongsTo('App\Models\Intrasm\Agencypintu','id_agcy')
       ->select(['id_agcyptu', 'nama_agencypintu']);
    }

    public function Advertiser(){
       return $this->belongsTo('App\Models\Intrasm\Advertiser','id_adv')
       ->select(['id_adv', 'nama_adv']);
    }

    public function Cam_partner(){
        return $this->hasMany('App\Models\Cam\Cam_partner','id_cam')
        ->select(['id', 'id_cam', 'Cam_partner.user_id','b.ID_USER'])->leftJoin('tbl_user as b','b.USER_ID','Cam_partner.user_id');
    }

    public function Cam_client(){
        return $this->hasMany('App\Models\Cam\Cam_client','id_cam')
        ->select(['id', 'id_cam', 'id_client_account'])
        ->whereNull('deleted_at');
    }

    public function Cam_cost(){
        return $this->hasMany('App\Models\Cam\Cam_Cost','id_cam')
        ->select(['id_cam_cost', 'id_cam', 'cost', 'title', 'cost_description', 'cost_by', 'client']);
    }

    public function User(){
        return $this->belongsTo('App\User', 'insert_user', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }

    public function user_am(){
        return $this->belongsTo('App\User', 'id_am', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }
    public function user_sgm(){
        return $this->belongsTo('App\User', 'id_sgm', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }
    public function user_sm(){
        return $this->belongsTo('App\User', 'id_sm', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }
    public function user_gm(){
        return $this->belongsTo('App\User', 'id_gm', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }

    public function User_client_history(){
        return $this->belongsTo('App\User', 'id_am', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }

    public function Commen(){
        return $this->hasMany('App\Models\Cam\Cam_comment','id_cam')
        ->select(['id_comment', 'id_cam', 'user_id', 'comment', 'created_at', \DB::raw('DATE_FORMAT(created_at, "%M")as bulan_komen'), \DB::raw('DATE_FORMAT(created_at, "%d")as tanggal_komen')]);
    }

    public function Client_history(){
        return $this->hasOne('App\Models\Cam\Cam_client', 'id_cam')
        ->whereNull('deleted_at');
    }

    public function User_picam(){
        return $this->belongsTo('App\User', 'pic_am', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }    
}