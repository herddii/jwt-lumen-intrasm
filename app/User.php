<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];
    
    protected $table="tbl_user";
    protected $primaryKey="ID_USER";
    const UPDATED_AT="UPDATE_DATE";
    const CREATED_AT="INSERT_DATE";

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


     public function am(){
        return $this->hasOne('App\Models\Sam\Sam','pic_am');
    }

    public function pic_sam(){
        return $this->hasOne('App\Models\Sam\Sam','pic_sam');
    }

    public function section(){
        return $this->belongsTo('App\Models\Intrasm\Section','ID_SECTION','ID_SECTION')
            ->select(array('ID_SECTION','section_name'));
    }

     public function picsection(){
        return $this->belongsTo('App\Models\Sam\Picsection','ID_SECTION','id_section')
            ->select(['id_picsection','id_req_type','id_section','id_bu']);
    }
    
    public function activity(){
        return $this->hasOne('App\Models\Sam\Activity','insert_user');
    }
    
    public function departement(){
        return $this->belongsTo('App\Models\Intrasm\Userdepartment','ID_DEPARTEMENT')
            ->select(['ID_DEPARTEMENT','departement_name','dept_name']);
    }

    public function approve(){
        return $this->hasOne('App\User','USER_ID','approved_by')
            ->select('USER_ID','USER_NAME');
    }

    public function sm(){
        return $this->hasOne('App\Models\Intrasm\Am','id_sm','user_id');
    }


    public function bu(){
        return $this->belongsTo('App\Models\Bu','ID_BU')
            ->select(
                [
                    'ID_BU',
                    'bu_full_name',
                    'bu_short_name',
                    'image',
                    'type'
                ]
            );
    }



}


