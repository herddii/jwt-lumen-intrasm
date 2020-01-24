<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Userclient_picsgm extends Model{
    
    protected $connection="intrasm";
    protected $table="tbl_userclient_picsgm";
    protected $primaryKey="id";
    protected $hidden =[
        'insert_user',
        'update_user',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $appends = [
        'view_url',
        'delete_url'
    ];
}