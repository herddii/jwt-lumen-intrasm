<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Userclient_picsgm extends Model{
    
    protected $connection="intrasm";
    protected $table="tbl_userclient_picsgm";
    protected $primaryKey="id_client_account";
    protected $hidden =[
        'insert_user',
        'update_user'
    ];
}