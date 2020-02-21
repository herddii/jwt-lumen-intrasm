<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_userclient_info extends Model
{
    // protected $connection="cam"; 
    protected $table="tbl_userclient_info";
    protected $primaryKey="id_client_info";

    use SoftDeletes;

    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;   
}