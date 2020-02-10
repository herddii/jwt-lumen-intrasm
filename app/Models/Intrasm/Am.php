<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Am extends Model{
    protected $connection="cam";
    protected $table="tbl_am";
    // protected $primaryKey="ID_AM";

    protected $hidden =[
        'insert_user',
        'update_user',
        'delete_user',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // protected $appends = [
    //     'view_url',
    //     'delete_url'
    // ];
    public function getUser(){
        return $this->belongsTo('App\User', 'id_am', 'USER_ID')
        ->select(['ID_USER', 'USER_ID', 'USER_NAME']);
    }
    // public function getDeleteUrlAttribute() {
    //     return route( 'am.delete', [ 'id' => $this->ID_AM ] );
    // }
    
    // public function getViewUrlAttribute() {
    //     return route( 'am.view', [ 'id' => $this->ID_AM ] );
    // }
}