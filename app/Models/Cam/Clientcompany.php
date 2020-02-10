<?php

namespace App\Models\Cam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Clientcompany extends Model
{
    protected $table="tbl_userclient_account";
    protected $primaryKey="id_client_account";
    protected $appends=array('company');

    use SoftDeletes;
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    
    public static $rules=[
        'email'=>'required',
        'type_company'=>'required',
        'id_company'=>'required',
        'position'=>'required',
        'id_level'=>'required',
    ];

    public static $pesan=[
        'email.required'=>'this field required',
        'type_company.required'=>'this field required',
        'id_company.required'=>'this field required',
        'position.required'=>'this field required',
        'id_client_level.required'=>'this field required',
    ];

    function Clientpicsgm(){
        return $this->hasMany('App\Models\Cam\Clientpicsgm','id_client_account')
        ->select(['id_client_account', 'id_sgm'])
        ->where('id_bu', \Auth::user()->ID_BU);
    }

    function Picsgm_reminder(){
        return $this->hasMany('App\Models\Cam\Clientpicsgm','id_client_account')
        ->select(['id_client_account', 'id_sgm', 'id_bu']);
    }

    function Picsgm_reminder_rcti(){
        return $this->hasMany('App\Models\Cam\Clientpicsgm','id_client_account')
        ->select(['id_client_account', 'id_sgm', 'id_bu'])
        ->where('id_bu',1);
    }

    function Picsgm_reminder_mnctv(){
        return $this->hasMany('App\Models\Cam\Clientpicsgm','id_client_account')
        ->select(['id_client_account', 'id_sgm', 'id_bu'])
        ->where('id_bu',2);
    }

    function Picsgm_reminder_gtv(){
        return $this->hasMany('App\Models\Cam\Clientpicsgm','id_client_account')
        ->select(['id_client_account', 'id_sgm', 'id_bu'])
        ->where('id_bu',3);
    }

    function Picsgm_reminder_inews(){
        return $this->hasMany('App\Models\Cam\Clientpicsgm','id_client_account')
        ->select(['id_client_account', 'id_sgm', 'id_bu'])
        ->where('id_bu',8);
    }

    public function Client(){
        return $this->belongsTo('App\Models\Cam\Client','id_client')
            ->select(['id_client','firstname', 'lastname','gender','birth_date','address','id_city','id_religion','photo','id_etnis','no_passport','name_passport','expired_passport', 'last_education', 'no_of_children', 'status', 'hobby', 'update_user']);
    }

    public function Client_agc(){
        return $this->belongsTo('App\Models\Cam\Client','id_client')
            ->select(['id_client','firstname', 'lastname','gender','birth_date','address','id_city','id_religion','photo','id_etnis','no_passport','name_passport','expired_passport', 'last_education', 'no_of_children', 'status', 'hobby', 'update_user']);
    }

    public function Client_adv(){
        return $this->belongsTo('App\Models\Cam\Client','id_client')
            ->select(['id_client','firstname', 'lastname','gender','birth_date','address','id_city','id_religion','photo','id_etnis','no_passport','name_passport','expired_passport', 'last_education', 'no_of_children', 'status', 'hobby', 'update_user']);
    }

    public function Clientlevel(){
        return $this->belongsTo('App\Models\Cam\Userclientlevel','id_client_level');
    }

    public function Advertiser(){
        return $this->belongsTo('App\Models\Dashboard\Advertiser','id_company');
    }

    public function Agencypintu(){
        return $this->belongsTo('App\Models\Dashboard\Agencypintu','id_company');
    }

    function eventclient(){
        return $this->hasMany('App\Models\Cam\Eventclient','id_cam_event');
    }

    function userclient(){
        return $this->hasMany('App\Models\Cam\Userclient','id_client', 'id_client')
        ->select(['id_client','firstname', 'lastname', 'gender', 'birth_place', 'birth_date', 'firstname', 'lastname', 'hobby', 'no_of_children', 'status', 'last_education', 'update_profile_advance']);
    }

    public function userclient_description(){
        return $this->hasMany('App\Models\Cam\Description', 'id_client_account', 'id_client_account')
        ->where('id_bu', \Auth::User()->ID_BU);
    }

    function userclientatasan(){
        return $this->hasMany('App\Models\Cam\Userclient','id_client', 'id_atasan')
        ->select(['id_client','firstname', 'lastname', 'gender', 'birth_place', 'birth_date', 'firstname', 'lastname']);
    }

    public function getCompanyAttribute($value)
    {
        $type=$this->type_company;
        $idcompany=$this->id_company;

        if($type=="ADV")
        {
            return $company=\App\Models\Dashboard\Advertiser::select(
                'id_adv as id',
                'nama_adv as name',
                'cluster',
                'anniversary',
                'id_city',
                'address'
            )->find($idcompany);
        }
        elseif($type=="AGC")
        {
            return $company=\App\Models\Dashboard\Agencypintu::select(
                'id_agcyptu as id',
                'nama_agencypintu as name',
                'cluster',
                'anniversary',
                'id_city',
                'address'
            )->find($idcompany);
        }  
    }

    public function info()
    {
        return $this->belongsTo('App\Models\Cam\Userclient','id_client')
            ->select(
                'id_client',
                'firstname',
                'lastname',
                'gender',
                'birth_place',
                'birth_date',
                'address',
                'id_city',
                'id_religion',
                // 'group_religion',
                'id_etnis',
                'no_passport',
                'name_passport',
                'expired_passport',
                'status',
                'no_of_children',
                'no_of_sibling',
                'order_of_children',
                'last_education',
                'photo',
                'active'
            );
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Cam\Userclientlevel','id_client_level')
            ->select(
                [
                    'id_client_level',
                    'name_level',
                    'description'
                ]
            );
    }

    public function picsgm(){
        return $this->hasMany('App\Models\Cam\Clientpicsgm','id_client_account');
    }

    public function am()
    {
        return $this->belongsToMany('\App\User','cam.tbl_userclient_picsgm','id_client_account','id_sgm','id_client_account','email')
            ->select(
                [
                    'tbl_user.ID_USER',
                    'username',
                    'email',
                    'name',
                    'gender',
                    'position',
                    'id_section',
                    'id_departement',
                    'tbl_user.ID_BU',
                    'images'
                ]
            )
            ->withPivot(
                [
                    'id_client_account',
                    'id_sgm',
                    'id_bu',
                    'active'
                ]
            );
    }    
}
