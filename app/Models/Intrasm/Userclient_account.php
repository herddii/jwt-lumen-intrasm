<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Userclient_account extends Model{
    protected $connection="intrasm";
    protected $table="tbl_userclient_account";
    protected $primaryKey="id_client_account";

    protected $hidden =[
        'insert_user',
        'update_user',
        'delete_user',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'userclient_account.delete', [ 'id' => $this->id_client_account ] );
    }

    public function getViewUrlAttribute() {
        return route( 'userclient_account.view', [ 'id' => $this->id_client_account ] );
    }
    
    public function Edit_namaclient(){
        return $this->hasOne('App\Models\Intrasm\Userclient', 'id_client', 'id_client')
        ->select(['id_client', 'firstname', 'lastname', \DB::raw('CONCAT(firstname, " ", lastname) as nama')]);
    }
}